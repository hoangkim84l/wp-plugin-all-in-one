<?php
if (!defined('ABSPATH')) exit;

add_action('wp_footer', function () {
    // Only show on frontend
    if (is_admin()) return;
    ?>
    <style>
        #myaio-scroll-to-top {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 45px;
            height: 45px;
            background-color: #333;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        #myaio-scroll-to-top:hover {
            background-color: #555;
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.3);
        }
        #myaio-scroll-to-top.show {
            opacity: 1;
            visibility: visible;
        }
        #myaio-scroll-to-top svg {
            width: 24px;
            height: 24px;
            fill: currentColor;
        }
    </style>
    <div id="myaio-scroll-to-top" title="<?php esc_attr_e('Scroll to Top', 'my-woo-aio'); ?>">
        <svg viewBox="0 0 24 24"><path d="M12 4l-8 8h6v8h4v-8h6z"/></svg>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var btn = document.getElementById('myaio-scroll-to-top');
            if(!btn) return;
            
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    btn.classList.add('show');
                } else {
                    btn.classList.remove('show');
                }
            });

            btn.addEventListener('click', function(e) {
                e.preventDefault();
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        });
    </script>
    <?php
});
