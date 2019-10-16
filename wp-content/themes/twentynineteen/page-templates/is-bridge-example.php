<?php

// Template Name: IS Bridge Example

get_header();

?>

<section id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php
            print_r(apply_filters('get_inf_contacts', 38 ,['FirstName','LastName','Email'], 'array' ));
        ?>
    </main>
</section>

<?php

get_footer();

?>

