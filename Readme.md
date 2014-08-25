# Carousel

This module for Thelia add a customizable carousel on your home page. You can upload you own image and overload the default template in your template for using the carousel.

# /!\ WARNING

this module is in development and can only be used with the current master branch of Thelia. This module is not compatible with Thelia 2.0.*

## Installation

* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is Carousel.
* Activate it in your thelia administration panel

## Usage

In the configuration panel of this module, you can upload how many images you want.

## Loop

The carousel's loop allows you to customize each images like the image's loop. You can set a width, a height, etc

### Input arguments

|Argument   |Description |
|---          |--- |
|**width**  | A width in pixels, for resizing image. If only the width is provided, the image ratio is preserved. Example : width="200" |
|**height** | A height in pixels, for resizing image. If only the height is provided, the image ratio is preserved. example : height="200" |
|**rotation**   |The rotation angle in degrees (positive or negative) applied to the image. The background color of the empty areas is the one specified by 'background_color'. example : rotation="90" |
|**background_color** |The color applied to empty image parts during processing. Use $rgb or $rrggbb color format.  example : background_color="$cc8000"|
|**quality** |The generated image quality, from 0(!) to 100%. The default value is 75% (you can hange this in the Administration panel).  example : quality="70"|
|**resize_mode** | If 'crop', the image will have the exact specified width and height, and will be cropped if required. If 'borders', the image will have the exact specified width and height, and some borders may be added. The border color is the one specified by 'background_color'. If 'none' or missing, the image ratio is preserved, and depending od this ratio, may not have the exact width and height required. resize_mode="crop"|
|**effects** |One or more comma separated effects definitions, that will be applied to the image in the specified order. Please see below a detailed description of available effects
Expected values :

* gamma:value : change the image Gamma to the specified value. Example: gamma:0.7.
* grayscale or greyscale : switch image to grayscale.
* colorize:color : apply a color mask to the image. The color format is $rgb or $rrggbb. Example: colorize:$ff2244.
* negative : transform the image in its negative equivalent.
* vflip or vertical_flip : flip the image vertically.
* hflip or horizontal_flip : flip the image horizontally.

example : effects="greyscale,gamma:0.7,vflip"  |

### Ouput arguments

|Variable   |Description |
|---          |--- |
|$ID    |the image ID |
|$IMAGE_URL    |The absolute URL to the generated image  |
|$ORIGINAL_IMAGE_URL    |The absolute URL to the original image  |
|$IMAGE_PATH    |The absolute path to the generated image file  |
|$ORIGINAL_IMAGE_PATH   |The absolute path to the original image file  |
|$ALT   |alt text |

### Exemple

```
{loop type="carousel" name="carousel.front" width="1200" height="390" resize_mode="borders"}
    <img src="{$IMAGE_URL}" alt="{$ALT}">
{/loop}
```

## How to overload ?

If you want your own carousel in your tempalte, create the directory ```modules/Carousel``` then create the template ```carousel.html``` in this directory. Here you can create your own carousel and the replace the default template provided in the module.
