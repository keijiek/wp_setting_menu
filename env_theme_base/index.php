<?php
get_header();

if (have_posts()) {
  while (have_posts()) {
    the_post();
    // 投稿の内容を表示するためのコード
?>
    <section class="container mx-auto mt-5">
      <?php
      if (!is_front_page()) {
      ?>
        <h2><?= the_title(); ?></h2>
      <?php
      }
      the_content();


      vardump(get_option('contact_address'));

      if (is_singular(include\post_types\Photos::getSlug())) {
        $gallery = get_post_meta(get_the_ID(), 'photo_gallery', true);
        foreach ($gallery as $image_id) {
          echo wp_get_attachment_image($image_id, 'medium');
        }
      }

      ?>
    </section>
<?php
  }
}

get_footer();
