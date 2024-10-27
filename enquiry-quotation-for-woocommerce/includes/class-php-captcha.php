<?php

class PISOL_ENQ_CaptchaGenerator{

    private $width;
    private $height;
    private $captchaLength;
    private $fontPath;
    private $captchaCode;
    private $useGD;

    public static $instance = null;

    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct($width = 200, $height = 50, $captchaLength = 6, $fontPath = 'Arial.ttf')
    {
        
        $this->width = $width;
        $this->height = $height;
        $this->captchaLength = $captchaLength;
        $this->fontPath = $fontPath;

        // Check for GD and Imagick availability
        if (extension_loaded('gd')) {
            $this->useGD = 'gd';
        } elseif (extension_loaded('imagick')) {
            $this->useGD = 'imagick';
        } else {
            $this->useGD = false;
        }

        // Hook to enqueue scripts
        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);

        // Register AJAX actions
        add_action('wc_ajax_generate_captcha', [$this, 'generateCaptcha']);
        add_action('wc_ajax_refresh_captcha', [$this, 'refreshCaptcha']);

    }

    static function image_library_available()
    {
        $instance = self::getInstance();
        return $instance->useGD !== false;
    }

    public function enqueueScripts()
    {
        // Enqueue jQuery (if it's not already loaded)
        wp_enqueue_script('jquery');

        // Inline script for refreshing CAPTCHA
        $script = "
        jQuery(document).ready(function ($) {
            $(document).on('click','#refresh-captcha', function () {
                $.get('" .home_url('?wc-ajax=refresh_captcha'). "', function (data) {
                    $('#captcha-image').attr('src', data); // Update image src with new data URL
                }).fail(function () {
                    console.error('Error refreshing CAPTCHA');
                });
            });
        });
        ";

        wp_add_inline_script('jquery', $script);
    }

    public function addCaptchaField(){
        ob_start(); // Start output buffering
        if(!self::image_library_available()){
            echo '<p class="library-error">' . esc_html__('Image generation module not installed in your server, make sure to install GD or Imagick library for PHP', 'pisol-enquiry-quotation-woocommerce') . '</p>';
            error_log('Image generation module not installed in your server, make sure to install GD or Imagick library for PHP');
            return ob_get_clean();
        }
        ?>
        <div class="pi-row-flex captcha-setup">
            <div class="pi-col-4">
                <input type="text" name="captcha_input" required placeholder="<?php esc_html_e('Enter captcha code', 'pisol-enquiry-quotation-woocommerce'); ?>">
            </div>
            <div class="pi-col-4 captcha-image-container">
                <img id="captcha-image" src="<?php echo esc_url( home_url('?wc-ajax=generate_captcha') ); ?>" alt="CAPTCHA">
                <img src="<?php echo plugin_dir_url( __DIR__ );?>/public/img/refresh.svg" id="refresh-captcha" title="<?php esc_html_e('Change captcha image', 'pisol-enquiry-quotation-woocommerce'); ?>"/>
            </div>
        </div>
        <?php
        return ob_get_clean(); // Return the buffered content
    }

    public function generateCaptcha()
    {
        // Generates the CAPTCHA image
        $this->generateCaptchaCode();
        $this->generateCaptchaImage();
        exit; // Prevent further execution
    }

    public function refreshCaptcha()
    {
        // Generate and return a new CAPTCHA image in base64 format
        ob_start();
        $this->generateCaptchaCode();
        $this->generateCaptchaImage();
        $imageData = ob_get_contents();
        ob_end_clean();
        echo 'data:image/png;base64,' . base64_encode($imageData);
        exit; // Prevent further execution
    }

    public function generateCaptchaImage()
    {
        if ($this->useGD == 'gd') {
            return $this->generateCaptchaImageGD($this->captchaCode);
        } elseif($this->useGD == 'imagick') {
            return $this->generateCaptchaImageImagick($this->captchaCode);
        }
    }

    private function generateCaptchaCode()
    {   
        // we removed capital I and small l as they look similar in Arial font
        $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
        $this->captchaCode = '';
        for ($i = 0; $i < $this->captchaLength; $i++) {
            $this->captchaCode .= $characters[rand(0, strlen($characters) - 1)];
        }

        if(isset(WC()->session)){
            WC()->session->set( 'captcha_code', $this->captchaCode );
        }else{
            error_log('WC session not found');
        }

        return $this->captchaCode;
    }

    private function generateCaptchaImageGD($captchaCode)
    {
        // GD-based image generation
        $image = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($image, 255, 255, 255); // White background
        imagefill($image, 0, 0, $bgColor);

        // Add noise (random lines)
        for ($i = 0; $i < 10; $i++) {
            $lineColor = imagecolorallocate($image, rand(100, 200), rand(100, 200), rand(100, 200));
            imageline($image, rand(0, $this->width), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height), $lineColor);
        }

        // Add the CAPTCHA text
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
        $fontSize = 30;
        $x = 10; // Starting x position
        $character_spacing = 30;
        for ($i = 0; $i < strlen($captchaCode); $i++) {
            $angle = rand(-10, 10); // Random angle
            $y = rand(30, 40); // Random y position
            imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $this->fontPath, $captchaCode[$i]);
            $x += $character_spacing; // Increment x position
        }

        // Output image
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
    }

    private function generateCaptchaImageImagick($captchaCode)
    {
        // Imagick-based image generation
        $image = new Imagick();
        $image->newImage($this->width, $this->height, new ImagickPixel('white'));

        $this->addNoise($image);
        $this->addCaptchaText($image, $captchaCode);
        $image->swirlImage(20);

        header('Content-Type: image/png');
        $image->setImageFormat('png');
        echo $image;

        $image->clear();
        $image->destroy();
    }

    private function addNoise(Imagick $image)
    {
        $draw = new ImagickDraw();
        for ($i = 0; $i < 10; $i++) {
            $draw->setStrokeColor(new ImagickPixel(sprintf('rgb(%d,%d,%d)', rand(100, 200), rand(100, 200), rand(100, 200))));
            $draw->setStrokeWidth(1);
            $draw->line(rand(0, $this->width), rand(0, $this->height), rand(0, $this->width), rand(0, $this->height));
        }
        $image->drawImage($draw);
    }

    private function addCaptchaText(Imagick $image, $captchaCode)
    {
        $draw = new ImagickDraw();
        $draw->setFillColor(new ImagickPixel('black'));
        $draw->setFont($this->fontPath);
        $draw->setFontSize(30);

        $x = 10;
        $characterSpacing = 30;
        for ($i = 0; $i < strlen($captchaCode); $i++) {
            $angle = rand(-10, 10);
            $y = rand(30, 40);
            $draw->annotation($x, $y, $captchaCode[$i]);
            $x += $characterSpacing;
        }

        $image->drawImage($draw);
    }

    static function validateCaptcha($userInput)
    {   
        if(isset(WC()->session)){
           $code = WC()->session->get( 'captcha_code');

            if($userInput === $code){
                return true;
            }
        }else{
            error_log('WC session not found while validating captcha');
        }

        return false;
    }

    static function captcha_enabled()
    {
        $type = get_option('pi_eqw_captcha', '');

        if ($type == 'captcha' && self::image_library_available()) {
            return true;
        }

        return false;
    }


}

PISOL_ENQ_CaptchaGenerator::getInstance();