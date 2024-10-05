# Custom Image Slider Plugin Documentation
## Overview
The Custom Image Slider plugin allows users to easily add and manage image sliders for WordPress posts and pages. It provides a user-friendly interface for selecting and organizing slider images directly from the post/page edit screen.

## Features
- Easy image selection using the WordPress Media Library
- Drag-and-drop functionality for image reordering
- Simple image removal
- Automatic saving of slider images with the post/page

## Installation
1. Upload the custom-image-slider folder to the /wp-content/plugins/ directory
2. Activate the plugin through the 'Plugins' menu in WordPress

## Usage
### Adding Images to the Slider 
1. Edit a post or page where you want to add the image slider 
2. Scroll down to the 'Slider Images' meta box 
3. Click the 'Add Slide' button 
4. Select one or more images from the Media Library 
5. Click 'Use Image' to add the selected images to the slider

### Managing Slider Images
- To remove an image: Click the '✖' icon on the top-right corner of the image thumbnail
- To reorder images: Drag and drop the image thumbnails to change their order

### Displaying the Slider
(Note: The current code doesn't include a function to display the slider on the front-end. You'll need to implement this separately.)

## Developer Notes
### Hooks and Filters
- add_meta_boxes action: Used to add the slider meta box to post edit screens
- save_post action: Used to save the slider images when a post is saved

### Functions
- cis_add_slider_meta_box(): Adds the meta box to the post edit screen
- cis_slider_images_callback($post): Renders the content of the meta box
- cis_save_slider_images($post_id): Saves the slider images when a post is saved

### Data Storage
Slider images are stored as an array in the post meta with the key _slider_images.

## Customization
To add support for custom post types, modify the $post_types array in the cis_add_slider_meta_box() function.

# TODO
- Add a settings menu in the WordPress admin area for global plugin configuration
- Implement a function or shortcode to display the slider on the front-end
- Add options for slider behavior (e.g., autoplay, transition effects)