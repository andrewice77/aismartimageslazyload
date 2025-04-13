# Smart Lazy Load for PrestaShop

A module for PrestaShop to enhance the performance of your store by implementing a smart lazy load for images. It allows you to load images only when they are about to appear in the viewport, reducing initial page load time and improving user experience.

This module also provides a customizable placeholder that can be configured in the back office, which will be displayed while the image is loading.

## Features

- **Lazy Loading**: Automatically applies lazy loading to all images on your site.
- **Customizable Placeholder**: Upload your own placeholder image or use a default SVG placeholder.
- **CSS Class Exclusions**: Option to exclude certain elements from lazy loading by their CSS classes.

## Installation

1. Download the module file.
2. In the PrestaShop back office, go to **Modules > Module Manager**.
3. Click **Upload a Module** and upload the ZIP file.
4. Install the module from the **Module Manager**.

## Configuration

Once the module is installed, you can configure it by going to:

**Modules > Module Manager > Smart Lazy Load > Configure**

### Available Configuration Options:

- **Enable Lazy Load**: Turn on or off lazy loading for images.
- **Exclude CSS Classes**: Enter a comma-separated list of CSS classes to exclude from lazy loading.
- **Upload Placeholder Image**: Upload an image that will be used as a placeholder while images are loading.

### Placeholder Image

You can upload your own placeholder image (e.g., a low-resolution image or an icon). If no image is uploaded, the module will use a default SVG placeholder.

## Usage

After configuring the module, lazy loading will be automatically applied to all images on your site. The images will be replaced with the placeholder until they enter the viewport, at which point they will be loaded.

## Development

If you would like to contribute to this module or customize it further, you can find the source code on the [GitHub repository](https://github.com/andrewice77/aiImagesLazyLoad.git).

### License

This module is open-source and released under the [MIT License](https://opensource.org/licenses/MIT).

## Support

For any issues or questions, feel free to open an issue on the GitHub repository or contact support.

---

Thank you for using **Smart Lazy Load** for PrestaShop! 🚀
