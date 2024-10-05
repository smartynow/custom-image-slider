<?php
/**
 * Plugin Name: Custom Image Slider
 * Description: A custom image slider for posts with easy image selection and management.
 * Version: 1.0
 * Author: smartynow
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add meta box for image selection
function cis_add_slider_meta_box() {
    $post_types = array('page'); // Add your custom post types here

    foreach ($post_types as $post_type) {
        add_meta_box(
            'cis_slider_images',
            'Slider Images',
            'cis_slider_images_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'cis_add_slider_meta_box');

// Meta box callback function
function cis_slider_images_callback($post) {
    $slider_images = get_post_meta($post->ID, '_slider_images', true);
    $slider_images = is_array($slider_images) ? $slider_images : array();

    wp_nonce_field('save_slider_images', 'slider_images_nonce');
    ?>
    <div class="slider-images">
        <ul class="image-list">
            <?php
            if (!empty($slider_images)) {
                foreach ($slider_images as $image_id) {
                    $image_src = wp_get_attachment_image_src($image_id, 'thumbnail')[0];
                    ?>
                    <li class="image-item">
                        <div class="image-container">
                            <img src="<?php echo esc_url($image_src); ?>" data-id="<?php echo esc_attr($image_id); ?>" alt="" />
                            <span class="remove-image" data-id="<?php echo esc_attr($image_id); ?>">✖</span>
                        </div>
                    </li>
                    <?php
                }
            }
            ?>
        </ul>
        <button type="button" class="button-add-image button">Add Slide</button>
        <input type="hidden" name="slider_images" value="<?php echo esc_attr(implode(',', $slider_images)); ?>">
    </div>

    <style>
        .slider-images {
            overflow-x: auto;
        }

        .image-list {
            display: flex;
            list-style-type: none;
            padding: 0;
            margin: 0;
        }

        .image-item {
            margin-right: 10px;
        }

        .image-container {
            position: relative;
            display: inline-block;
        }

        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            width: 25px;
            height: 25px;
            text-align: center;
            line-height: 25px;
            cursor: pointer;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addImageBtn = document.querySelector('.button-add-image');
            const imageList = document.querySelector('.image-list');
            const hiddenInput = document.querySelector('input[name="slider_images"]');

            const frameInit = () => {
                const frame = wp.media({
                    title: 'Select Images',
                    button: {
                        text: 'Use Image'
                    },
                    multiple: true
                });

                frame.on('select', () => {
                    const images = frame.state().get('selection').toArray();
                    const imageIDs = images.map(image => image.id);

                    imageIDs.forEach(id => {
                        const attachment = wp.media.attachment(id);
                        attachment.fetch();

                        const listItem = document.createElement('li');
                        listItem.classList.add('image-item');
                        listItem.innerHTML = `<div class="image-container">
                                                <img src="${attachment.get('sizes').thumbnail.url}" data-id="${id}" alt="" />
                                                <span class="remove-image" data-id="${id}">✖</span>
                                              </div>`;
                        imageList.appendChild(listItem);
                    });

                    const existingValue = hiddenInput.value ? hiddenInput.value.split(',') : [];
                    const newValue = existingValue.concat(imageIDs);
                    hiddenInput.value = newValue.join(',');
                });

                frame.open();
            };

            addImageBtn.addEventListener('click', (e) => {
                e.preventDefault();
                frameInit();
            });

            imageList.addEventListener('click', (e) => {
                if (e.target.classList.contains('remove-image')) {
                    const imageItem = e.target.closest('.image-item');
                    const imageID = e.target.dataset.id;
                    imageItem.remove();

                    const imageIDs = hiddenInput.value.split(',').filter(id => id !== imageID);
                    hiddenInput.value = imageIDs.join(',');
                }
            });
        });
    </script>
    <?php
}

// Save the selected images
function cis_save_slider_images($post_id) {
    if (!isset($_POST['slider_images_nonce']) || !wp_verify_nonce($_POST['slider_images_nonce'], 'save_slider_images')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['slider_images'])) {
        $slider_images = sanitize_text_field($_POST['slider_images']);
        $slider_images_array = explode(',', $slider_images);
        update_post_meta($post_id, '_slider_images', $slider_images_array);
    } else {
        delete_post_meta($post_id, '_slider_images');
    }
}
add_action('save_post', 'cis_save_slider_images');

// TODO Add settings menu in admin menu