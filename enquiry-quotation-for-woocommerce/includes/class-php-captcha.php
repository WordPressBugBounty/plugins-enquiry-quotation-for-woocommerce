<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

class PISOL_ENQ_CaptchaGenerator{

    static $instance = null;

    private $useGD = false;

    private $width = 200;

    private $height = 50;

    private $fontPath = '';

    private $captchaLength = 6;

    static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct() {


        if(! self::captcha_enabled() ) return;

        if (extension_loaded('gd')) {
            $this->useGD = 'gd';
        } elseif (extension_loaded('imagick')) {
            $this->useGD = 'imagick';
        }

        if($this->useGD === false) {
            add_action( 'admin_notices', function(){
                ?>
                <div class="error notice">
                    <p><?php esc_html_e( 'Image generation module not installed in your server, make sure to install GD or Imagick library for PHP to use Captcha for checkout page', 'pisol-enquiry-quotation-woocommerce' ); ?></p>
                </div>
                <?php
            } );
            return;
        }

        $this->fontPath = __DIR__.'/ARIAL.TTF';

        $this->captchaLength = $this->get_captcha_length();

        $this->width = $this->captcha_width();


        add_action('pi_eqw_add_captcha_field', [$this, 'custom_checkout_captcha_field']);

        add_action('wp_ajax_pi_enq_generate_captcha', [$this, 'send_generated_captcha_image']);
        add_action('wp_ajax_nopriv_pi_enq_generate_captcha', [$this, 'send_generated_captcha_image']);
        add_action('wp_ajax_pi_enq_refresh_captcha', [$this, 'refreshCaptcha']);
        add_action('wp_ajax_nopriv_pi_enq_refresh_captcha', [$this, 'refreshCaptcha']);

        add_action('wp_enqueue_scripts', [$this, 'enqueueScripts']);
        
    }


    public function custom_checkout_captcha_field() {

        $placeholder = $this->get_captcha_placeholder();
        $refresh_title = $this->get_refresh_captcha_title();

        echo '<div id="pi_enq_captcha_container">';
        echo '<div id="pi_enq_captcha">';
        echo '<input type="text" name="captcha_field" id="captcha_field" class="input-text" required placeholder="'.esc_attr($placeholder).'">';
        echo '<div class="captcha_image_container">';
        echo '<img src="' . esc_url( admin_url('admin-ajax.php?action=pi_enq_generate_captcha') ) . '" alt="CAPTCHA" id="captcha_image">';
        echo '</div>';
        echo '<a href="#" id="refresh_captcha" title="'.esc_attr($refresh_title).'"><img src="'.esc_url(plugin_dir_url( __FILE__ ).'img/refresh.svg').'" id="captcha_refresh_icon">.</a>';
        echo '</div>';
        echo '<label id="captcha_field-error" class="error" for="captcha_field"></label>';
        echo '</div>';
    }



    public function send_generated_captcha_image() {
        nocache_headers();
        $this->generate_captcha_image();
        wp_die();
    }

    public function refreshCaptcha()
    {
        // Generate and return a new CAPTCHA image in base64 format
        ob_start();
        $this->generate_captcha_image();
        $imageData = ob_get_contents();
        ob_end_clean();
        $data = 'data:image/png;base64,' . base64_encode($imageData);
        // Escape the data URI before outputting to prevent XSS
        echo esc_attr($data);
        wp_die(); // Prevent further execution
    }

    private function generateCaptchaCode()
    {   
        // we removed capital I and small l as they look similar in Arial font
        $characters = $this->get_characters();
        $captchaCode = '';
        for ($i = 0; $i < $this->captchaLength; $i++) {
            $captchaCode .= $characters[wp_rand(0, strlen($characters) - 1)];
        }

        return $captchaCode;
    }

    private function generate_captcha_image() {
        header('Content-Type: image/png');        
        $captcha_string = $this->generateCaptchaCode();
        
        WC()->session->set('captcha_code', $captcha_string);

        if($this->useGD === 'gd') {
            $this->generateCaptchaImageGD($captcha_string);
        } elseif($this->useGD === 'imagick') {
            $this->generateCaptchaImageImagick($captcha_string);
        }
    }

    private function generateCaptchaImageGD($captchaCode)
    {
        // GD-based image generation
        $image = imagecreatetruecolor($this->width, $this->height);
        $bgColor = imagecolorallocate($image, 255, 255, 255); // White background
        imagefill($image, 0, 0, $bgColor);

        // Add noise (random lines)
        for ($i = 0; $i < 10; $i++) {
            $lineColor = imagecolorallocate($image, wp_rand(100, 200), wp_rand(100, 200), wp_rand(100, 200));
            imageline($image, wp_rand(0, $this->width), wp_rand(0, $this->height), wp_rand(0, $this->width), wp_rand(0, $this->height), $lineColor);
        }

        // Add the CAPTCHA text
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
        $fontSize = 30;
        $x = 10; // Starting x position
        $character_spacing = 30;
        for ($i = 0; $i < strlen($captchaCode); $i++) {
            $angle = wp_rand(-10, 10); // Random angle
            $y = wp_rand(30, 40); // Random y position
            imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $this->fontPath, $captchaCode[$i]);
            $x += $character_spacing; // Increment x position
        }

        // Output image
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

        // Output binary image data safely. Use getImageBlob() to get raw
        // image data and avoid casting the Imagick object which PHPCS flags.
        $image->setImageFormat('png');
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- binary image blob
        echo $image->getImageBlob();

        $image->clear();
        $image->destroy();
    }

    private function addNoise(Imagick $image)
    {
        $draw = new ImagickDraw();
        for ($i = 0; $i < 10; $i++) {
            $draw->setStrokeColor(new ImagickPixel(sprintf('rgb(%d,%d,%d)', wp_rand(100, 200), wp_rand(100, 200), wp_rand(100, 200))));
            $draw->setStrokeWidth(1);
            $draw->line(wp_rand(0, $this->width), wp_rand(0, $this->height), wp_rand(0, $this->width), wp_rand(0, $this->height));
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
            $angle = wp_rand(-10, 10);
            $y = wp_rand(30, 40);
            $draw->annotation($x, $y, $captchaCode[$i]);
            $x += $characterSpacing;
        }

        $image->drawImage($draw);
    }

    public function enqueueScripts()
    {

        $script = "
        jQuery(document).ready(function ($) {
            $('body').on('click','#refresh_captcha', function (e) {
                e.preventDefault();
                jQuery('#pi_enq_captcha').addClass('loading');
                $.get('" .admin_url('admin-ajax.php?action=pi_enq_refresh_captcha'). "', function (data) {
                    $('#captcha_image').attr('src', data); // Update image src with new data URL
                    jQuery('#pi_enq_captcha').removeClass('loading');
                    jQuery('#captcha_field').val(''); // Clear the input field
                }).fail(function () {
                    console.error('Error refreshing CAPTCHA');
                    jQuery('#pi_enq_captcha').removeClass('loading');
                });
            });
        });
        ";

        wp_add_inline_script('jquery', $script);

        // Inline CSS for the loading spinner
        $color_scheme = '#cccccc';
        $color_scheme_error = '#ff0000';
        $css = "
            :root {
                --captcha_color: $color_scheme;
                --captcha_error_color: $color_scheme_error;
                --captcha_border:5px;
            }

            #pi_enq_captcha_container{
                display:block;
                width:100%;
                margin-bottom:20px;
                margin-top:20px;
            }

            #pi_enq_captcha{
                display:grid;
                grid-template-columns: 1fr 200px 50px;
                border:var(--captcha_border, 5px) solid var(--captcha_color, #ccc);
                border-radius:6px;
                max-width:600px;
            }

            @media (max-width: 600px) {
                #pi_enq_captcha{
                    grid-template-columns: 1fr;
                }
                
                #captcha_field{
                    border-bottom:1px solid var(--captcha_color, #ccc) !important;
                }
            }

            body:has([data-error-id='captcha-error']) #pi_enq_captcha{
                border:var(--captcha_border, 5px) solid var(--captcha_error_color, #ff0000);
            }

            #pi_enq_captcha.loading{
                opacity:0.5;
            }

            .captcha_image_container{
                padding:3px;
                text-align:center;
                border-left:1px solid var(--captcha_color, #ccc);
                background-color:#ffffff;
                display:flex;
                align-items:center;
            }

            #captcha_image{
                margin:auto;
            }

            #captcha_refresh_icon{
                width:30px;
            }

            #refresh_captcha{
                cursor:pointer;
                display:flex;
                align-items:center;
                justify-content:center;
                background:var(--captcha_color, #ccc);
                font-size:0px;
                border-left:1px solid var(--captcha_color, #ccc);
            }

            body:has([data-error-id='captcha-error']) #refresh_captcha{
                background:var(--captcha_error_color, #ff0000);
            }


            #captcha_field, #captcha_field:focus-visible, #captcha_field:focus{
                outline: none;
                border:none;
                padding:10px;
            }

            #pi_enq_captcha_container{
                grid-column:1/3;
            }

            #captcha_field-error:empty{
                display:none;
            }
        ";
        // Add custom CSS to the checkout page use dummy dependency 
        wp_register_style('pi-enq-captch-custom-inline-css', false);
        wp_enqueue_style('pi-enq-captch-custom-inline-css');
        wp_add_inline_style('pi-enq-captch-custom-inline-css', $css);
    }

    function get_captcha_placeholder() {
        $placeholder = get_option('pi_eqw_captcha_placeholder', 'Enter the CAPTCHA');
        return $placeholder;
    }

    function get_refresh_captcha_title() {
        return __('Refresh the CAPTCHA','pisol-enquiry-quotation-woocommerce');
    }

    function get_captcha_length() {
        $length = get_option('pi_eqw_captcha_length', 6);
        $length = absint( apply_filters('pi_enq_captcha_length', $length));
        return $length > 6 || $length < 1 ? 6 : $length;
    }

    function captcha_width() {
        $character_length = $this->get_captcha_length();
        $width = $character_length * 40;
        return $width;
    }

    function get_characters() {
        $type_of_string = get_option('pi_eqw_captcha_characters', 'mix');
        if($type_of_string === 'mix') {
            $characters = 'ABCDEFGHJKLMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz0123456789';
        } 

        if($type_of_string === 'numbers') {
            $characters = '0123456789';
        }

        if($type_of_string === 'capital_letter') {
            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        }

        if($type_of_string === 'small_letter') {
            $characters = 'abcdefghijklmnopqrstuvwxyz';
        }


        return apply_filters('pi_enq_captcha_characters', $characters);
    }

    static function captcha_enabled()
    {
        $type = get_option('pi_eqw_captcha', '');

        if ($type == 'captcha') {
            return true;
        }

        return false;
    }

    static function image_library_available()
    {
        $instance = self::get_instance();
        return $instance->useGD !== false;
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
}

// Instantiate the CAPTCHA class
PISOL_ENQ_CaptchaGenerator::get_instance();
