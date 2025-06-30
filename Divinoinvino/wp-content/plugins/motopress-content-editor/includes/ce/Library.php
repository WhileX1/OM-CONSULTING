<?php
require_once dirname(__FILE__) . '/BaseElement.php';
require_once dirname(__FILE__) . '/Element.php';
require_once dirname(__FILE__) . '/Group.php';
require_once dirname(__FILE__) . '/Object.php';
require_once dirname(__FILE__) . '/Template.php';

/**
 * Description of MPCELibrary
 *
 */
class MPCELibrary {
	/** @var MPCEGroup[] */
    private $library = array();
    public $globalPredefinedClasses = array();
    public $tinyMCEStyleFormats = array();
    private $templates = array();
    private $gridObjects = array();
    public static $isAjaxRequest;
    private static $defaultGroup;
	private static $instance = null;
    public $deprecatedParameters = array(
        'mp_button' => array(
            'color' => array(
                'prefix' => 'motopress-btn-color-'
            ),
            'size' => array(
                'prefix' => 'motopress-btn-size-'
            )
        ),
        'mp_accordion' => array(
            'style' => array(
                'prefix' => 'motopress-accordion-'
            )
        ),
        'mp_social_buttons' => array(
            'size' => array(
                'prefix' => ''
            ),
            'style' => array(
                'prefix' => ''
            )
        ),
        'mp_table' => array(
            'style' => array(
                'prefix' => 'motopress-table-style-'
            )
        )
    );

	/**
	 * 
	 * @return MPCELibrary
	 */
	public static function getInstance(){
		if (is_null(self::$instance)) {
			self::$instance = new MPCELibrary();
		}
		return self::$instance;
	}

    private function __construct() {
        self::$isAjaxRequest = $this->isAjaxRequest();

        $backgroundColor = array(
            'label' => 'Background Color',
            'values' => array(
                'blue' => array(
                    'class' => 'motopress-bg-color-blue',
                    'label' => 'Blue'
                ),
                'dark' => array(
                    'class' => 'motopress-bg-color-dark',
                    'label' => 'Dark'
                ),
                'gray' => array(
                    'class' => 'motopress-bg-color-gray',
                    'label' => 'Gray'
                ),
                'green' => array(
                    'class' => 'motopress-bg-color-green',
                    'label' => 'Green'
                ),
                'red' => array(
                    'class' => 'motopress-bg-color-red',
                    'label' => 'Red'
                ),
                'silver' => array(
                    'class' => 'motopress-bg-color-silver',
                    'label' => 'Silver'
                ),
                'white' => array(
                    'class' => 'motopress-bg-color-white',
                    'label' => 'White'
                ),
                'yellow' => array(
                    'class' => 'motopress-bg-color-yellow',
                    'label' => 'Yellow'
                )
            )
        );

        $style = array(
            'label' => 'Style',
            'allowMultiple' => true,
            'values' => array(
                'bg-alpha-75' => array(
                    'class' => 'motopress-bg-alpha-75',
                    'label' => 'Semi Transparent'
                ),
                'border' => array(
                    'class' => 'motopress-border',
                    'label' => 'Bordered'
                ),
                'border-radius' => array(
                    'class' => 'motopress-border-radius',
                    'label' => 'Rounded'
                ),
                'shadow' => array(
                    'class' => 'motopress-shadow',
                    'label' => 'Shadow'
                ),
                'shadow-bottom' => array(
                    'class' => 'motopress-shadow-bottom',
                    'label' => 'Bottom Shadow'
                ),
                'text-shadow' => array(
                    'class' => 'motopress-text-shadow',
                    'label' => 'Text Shadow'
                )
            )
        );

        $border = array(
            'label' => 'Border Side',
            'allowMultiple' => true,
            'values' => array(
                'border-top' => array(
                    'class' => 'motopress-border-top',
                    'label' => 'Border Top'
                ),
                'border-right' => array(
                    'class' => 'motopress-border-right',
                    'label' => 'Border Right'
                ),
                'border-bottom' => array(
                    'class' => 'motopress-border-bottom',
                    'label' => 'Border Bottom'
                ),
                'border-left' => array(
                    'class' => 'motopress-border-left',
                    'label' => 'Border Left'
                )
            )
        );

        $textColor = array(
            'label' => 'Text Color',
            'values' => array(
                'color-light' => array(
                    'class' => 'motopress-color-light',
                    'label' => 'Light Text'
                ),
                'color-dark' => array(
                    'class' => 'motopress-color-dark',
                    'label' => 'Dark Text'
                )
            )
        );

		$visiblePredefinedGroup = array(
			'label' => 'Visibility',
			'allowMultiple' => true,
			'values' => array(
				'hide-on-desktop' => array(
					'class' => 'motopress-hide-on-desktop',
					'label' => 'Hide On Desktop'
				),
				'hide-on-tablet' => array(
					'class' => 'motopress-hide-on-tablet',
					'label' => 'Hide On Tablet'
				),
				'hide-on-phone' => array(
					'class' => 'motopress-hide-on-phone',
					'label' => 'Hide On Phone'
				),
			)
		);

        $rowPredefinedStyles = array(
            'background-color' => $backgroundColor,
            'style' => $style,
            'border' => $border,
            'color' => $textColor,
			'visible' => $visiblePredefinedGroup
        );

        $spanPredefinedStyles = array(
            'background-color' => $backgroundColor,
            'style' => $style,
            'border' => $border,
            'color' => $textColor,
			'visible' => $visiblePredefinedGroup
        );

        $spacePredefinedStyles = array(
            'type' => array(
                'label' => 'Type',
                'values' => array(
                    'light' => array(
                        'class' => 'motopress-space-light',
                        'label' => 'Light'
                    ),
                    'normal' => array(
                        'class' => 'motopress-space-normal',
                        'label' => 'Normal'
                    ),
                    'dotted' => array(
                        'class' => 'motopress-space-dotted',
                        'label' => 'Dotted'
                    ),
                    'dashed' => array(
                        'class' => 'motopress-space-dashed',
                        'label' => 'Dashed'
                    ),
                    'double' => array(
                        'class' => 'motopress-space-double',
                        'label' => 'Double'
                    ),
                    'groove' => array(
                        'class' => 'motopress-space-groove',
                        'label' => 'Grouve'
                    ),
                    'ridge' => array(
                        'class' => 'motopress-space-ridge',
                        'label' => 'Ridge'
                    ),
                    'heavy' => array(
                        'class' => 'motopress-space-heavy',
                        'label' => 'Heavy'
                    )
                )
            )
        );
        /* Objects */
        //grid
        $rowParameters = array(
			'stretch' => array(
				'type' => 'select',
				'label' => __("Container Width:", 'motopress-content-editor'),
				'description' => sprintf(__("Set fixed width in <a href='%s' target='_blank'>plugin settings</a>", 'motopress-content-editor'), admin_url('admin.php?page=motopress_options')),
				'default' => '',
				'list' => array(
					'' => __("Auto", 'motopress-content-editor'),
					'full' => __("Full", 'motopress-content-editor'),
					'fixed' => __("Fixed", 'motopress-content-editor')
				),
			),
			'width_content' => array(
				'type' => 'select',
				'label' => 'Content Width',
				'default' => '',
				'list' => array(
					'' => __("Auto", 'motopress-content-editor'),
					'full' => __("Full", 'motopress-content-editor'),
					'fixed' => __("Fixed", 'motopress-content-editor')
				),
				'dependency' => array(
					'parameter' => 'stretch',
					'value' => 'full'
				)
			),
			'full_height' => array(
				'type' => 'checkbox',
				'label' => __("Fill the height of the window", 'motopress-content-editor'),
				'default' => 'false'
			),
            'bg_media_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Background:", 'motopress-content-editor'),
                'description' => __("Full preview is available at the website only. Select 'Full-Width' style to stretch media to the website width.", 'motopress-content-editor'),
//                'default' => 'disabled',
                'list' => array(
                    'disabled' => __("None", 'motopress-content-editor'),
                    'video' => __("Video", 'motopress-content-editor'),
                    'youtube' => __("YouTube", 'motopress-content-editor'),
                    'parallax' => __("Parallax", 'motopress-content-editor')
                )
            ),
            'bg_video_youtube' => array(
                'type' => 'video',
                'label' => __("YouTube video URL:", 'motopress-content-editor'),
                'default' => MPCEShortcode::DEFAULT_YOUTUBE_BG,
                'description' => __("Paste the URL of YouTube video you'd like to embed", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                )
            ),
            'bg_video_youtube_cover' => array(
                'type' => 'image',
                'label' => __("Select image to cover video", 'motopress-content-editor'),
                'description' => __("Cover image will be extended automatically", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                )
            ),
            'bg_video_youtube_repeat' => array(
                'type' => 'checkbox',
                'label' => __("Repeat", 'motopress-content-editor'),
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                )
            ),
            'bg_video_youtube_mute' => array(
                'type' => 'checkbox',
                'label' => __("Mute", 'motopress-content-editor'),
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'youtube'
                )
            ),
            'bg_video_webm' => array(
                'type' => 'media-video',
                'legend' => __("Select video from Media Library in webm, mp4 and ogg formats for cross-browser compatibility. Use <a href='http://www.mirovideoconverter.com/' target='_blank'>video converter</a> to convert your video", 'motopress-content-editor'),
                'label' => sprintf(__("Video source in %s format:", 'motopress-content-editor'), 'WEBM'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'bg_video_mp4' => array(
                'type' => 'media-video',
                'label' => sprintf(__("Video source in %s format:", 'motopress-content-editor'), 'MP4'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'bg_video_ogg' => array(
                'type' => 'media-video',
                'label' => sprintf(__("Video source in %s format:", 'motopress-content-editor'), 'OGV'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'bg_video_cover' => array(
                'type' => 'image',
                'label' => __("Select image to cover video", 'motopress-content-editor'),
                'description' => __("Cover image will be extended automatically", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'bg_video_repeat' => array(
                'type' => 'checkbox',
                'label' => __("Repeat", 'motopress-content-editor'),
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'bg_video_mute' => array(
                'type' => 'checkbox',
                'label' => __("Mute", 'motopress-content-editor'),
                'default' => 'true',
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'video'
                )
            ),
            'parallax_image' => array(
                'type' => 'image',
                'label' => __("Select image for parallax effect", 'motopress-content-editor'),
                'description' => __("Background image moves slower than the foreground content", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'parallax'
                )
            ),
            'parallax_bg_size' => array(
                'type' => 'select',
                'label' => __("Background size", 'motopress-content-editor'),
	            'default' => 'normal',
                'list' => array(
	                'normal' => __("Normal", 'motopress-content-editor'),
	                'cover' => __("Cover", 'motopress-content-editor'),
	                'contain' => __("Contain", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'bg_media_type',
                    'value' => 'parallax'
                )
            ),
//            'parallax_speed' => array(
//                'type' => 'spinner',
//                'label' => '',
//                'description' => '',
//                'default' => 0.5,
//                'min' => -5,
//                'max' => 5,
//                'step' => 0.1,
//                'dependency' => array(
//                    'parameter' => 'bg_media_type',
//                    'value' => 'parallax'
//                )
//            ),
			'id' => array(
				'type' => 'text',
				'label' => __("Element unique ID", 'motopress-content-editor'),
				'description' => __("Must start with a letter and contain dashes, underscores, letters or numbers", 'motopress-content-editor')
			)
        );
		
        $rowObj = new MPCEObject(MPCEShortcode::PREFIX . 'row', __("Row", 'motopress-content-editor'), null, $rowParameters, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
		$rowObjStyle = array(
			'mp_style_classes' => array(
				'predefined' => $rowPredefinedStyles,
				'additional_description' => __("Note: some styles may work at a live site only.", 'motopress-content-editor')
			),
			'mp_custom_style' => array(
				'limitation' => 'margin-horizontal'
			)
		);
        $rowObj->addStyle($rowObjStyle);

        $rowInnerObj = new MPCEObject(MPCEShortcode::PREFIX . 'row_inner', __("Inner Row", 'motopress-content-editor'), null, $rowParameters, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $rowInnerObj->addStyle($rowObjStyle);

        $spanObj = new MPCEObject(MPCEShortcode::PREFIX . 'span', __("Column", 'motopress-content-editor'), null, null, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
		$spanObjStyle = array(
			'mp_style_classes' => array(
				'predefined' => $spanPredefinedStyles
			),
			'mp_custom_style' => array(
				'limitation' => array('margin-horizontal')
			)
		);
        $spanObj->addStyle($spanObjStyle);

        $spanInnerObj = new MPCEObject(MPCEShortcode::PREFIX . 'span_inner', __("Inner Column", 'motopress-content-editor'), 'column.png', null, null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE);
        $spanInnerObj->addStyle($spanObjStyle);

		$this->setGrid(array(
            'row' => array(
                'shortcode' => 'mp_row',
                'inner' => 'mp_row_inner',
                'class' => 'mp-row-fluid',
                'edgeclass' => 'mp-row-fluid',
                'col' => '12'
            ),
            'span' => array(
                'type' => 'single',
                'shortcode' => 'mp_span',
                'inner' => 'mp_span_inner',
                'class' => 'mp-span',
                'attr' => 'col',
                'custom_class_attr' => 'classes'
            )
        ));

/* TEXT */
        $textObj = new MPCEObject(MPCEShortcode::PREFIX . 'text', __("Paragraph", 'motopress-content-editor'), 'text.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => __("Click the text box to add and edit your content or click Edit", 'motopress-content-editor') . ' ' . __("Paragraph", 'motopress-content-editor'),
                'text' => __("Edit", 'motopress-content-editor') . ' ' . __("Paragraph", 'motopress-content-editor')
            )
        ), 20, MPCEObject::ENCLOSED);
        $textPredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($textPredefinedStyles);
        $textObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $textPredefinedStyles,
                'additional_description' => sprintf(__("Note: go to Dashboard - %s Settings to add Google Fonts.", 'motopress-content-editor'), mpceSettings('brand_name'))
            )
        ));

/* HEADER */
        $headingObj = new MPCEObject(MPCEShortcode::PREFIX . 'heading', __("Title", 'motopress-content-editor'), 'heading.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => __("Click the text box to add and edit your content or click Edit", 'motopress-content-editor') . ' ' . __("Title", 'motopress-content-editor'),
                'text' => __("Edit", 'motopress-content-editor') . ' ' . __("Title", 'motopress-content-editor')
            )
        ), 10, MPCEObject::ENCLOSED);
        $headingPredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($headingPredefinedStyles);
        $headingObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $headingPredefinedStyles,
                'additional_description' => sprintf(__("Note: go to Dashboard - %s Settings to add Google Fonts.", 'motopress-content-editor'), mpceSettings('brand_name'))
            )
        ));

/* CODE */        
        $codeObj = new MPCEObject(MPCEShortcode::PREFIX . 'code', __("WordPress Text", 'motopress-content-editor'), 'wordpress.png', array(
            'button' => array(
                'type' => 'editor-button',
                'label' => '',
                'default' => '',
                'description' => __("Click the text box to add and edit your content or click Edit", 'motopress-content-editor') . ' ' . __("WordPress Text", 'motopress-content-editor'),
                'text' => __("Edit", 'motopress-content-editor') . ' ' . __("WordPress Text", 'motopress-content-editor')
            )
        ), 30, MPCEObject::ENCLOSED);
        $codePredefinedStyles = array();
        $this->extendPredefinedWithGoogleFonts($codePredefinedStyles);
        $codeObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $codePredefinedStyles,
                'additional_description' => sprintf(__("Note: go to Dashboard - %s Settings to add Google Fonts.", 'motopress-content-editor'),  mpceSettings('brand_name'))
            )
        ));

/* IMAGE */
        $imageObj = new MPCEObject(MPCEShortcode::PREFIX . 'image', __("Image", 'motopress-content-editor'), 'image.png', array(
            'id' => array(
                'type' => 'image',
                'label' => __("Select Image", 'motopress-content-editor'),
                'default' => '',
                'description' => __("Choose an image from Media Library", 'motopress-content-editor'),
                'autoOpen' => 'true'
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => __("Image size", 'motopress-content-editor'),
                'default' => 'full',
                'list' => array(
                    'full' => __("Full", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'medium' => __("Medium", 'motopress-content-editor'),
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => __("Image size in pixels, ex. 200x100 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'link_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Link to", 'motopress-content-editor'),
                'default' => 'custom_url',
                'list' => array(
                    'custom_url' => __("Custom URL", 'motopress-content-editor'),
                    'media_file' => __("Media File", 'motopress-content-editor'),
                    'lightbox' => __("Lightbox", 'motopress-content-editor')
                )
            ),
            'link' => array(
                'type' => 'link',
                'label' => __("Link to", 'motopress-content-editor'),
                'default' => '#',
                'description' => __("Click on image to open the link. (ex. http://yoursite.com/)", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'custom_url'
                )
            ),
            'rel' => array(
                'type' => 'text',
                'label' => __("Link 'rel' value for your custom lightbox", 'motopress-content-editor'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'media_file'
                )
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => __("Open link in new window (tab)", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'caption' => array(
                'type' => 'checkbox',
                'label' => __("Show image caption", 'motopress-content-editor'),
                'description' => __("You can set caption in media library", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            )
        ), 10);
        $imageObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-image-obj-basic',
                    'label' => 'Image'
                ),
                'selector' => 'img'
            ),
			'mp_custom_style' => array(
				'selector' => 'img',
				'limitation' => array(
					'margin'
				)
			)
        ));

/* GRID GALLERY */
        $gridGalleryObj = new MPCEObject(MPCEShortcode::PREFIX . 'grid_gallery', __("Grid Gallery", 'motopress-content-editor'),  'grid-gallery.png', array(
            'ids' => array(
                'type' => 'multi-images',
                'default' => '',
                'description' => __("Select images from Media Library", 'motopress-content-editor'),
                'text' => __("Organize Images", 'motopress-content-editor'),
                'autoOpen' => 'true'
            ),
            'columns' => array(
                'type' => 'radio-buttons',
                'label' => __("Columns count", 'motopress-content-editor'),
                'default' => 3,
                'list' => array(
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                )
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => __("Image size", 'motopress-content-editor'),
                'default' => 'thumbnail',
                'list' => array(
                    'full' => __("Full", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'medium' => __("Medium", 'motopress-content-editor'),
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => __("Image size in pixels, ex. 200x100 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'link_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Link to", 'motopress-content-editor'),
                'default' => 'lightbox',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'media_file' => __("Media File", 'motopress-content-editor'),
                    'attachment' => __("Attachment Page", 'motopress-content-editor'),
                    'lightbox' => __("Lightbox", 'motopress-content-editor'),
                )
            ),
            'rel' => array(
                'type' => 'text',
                'label' => __("Link 'rel' value for your custom lightbox", 'motopress-content-editor'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'link_type',
                    'value' => 'media_file'
                )
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => __("Open link in new window (tab)", 'motopress-content-editor'),
                'default' => 'false',
            ),
            'caption' => array(
                'type' => 'checkbox',
                'label' => __("Show image caption", 'motopress-content-editor'),
                'description' => __("You can set caption in media library", 'motopress-content-editor'),
                'default' => 'false',
            )
        ), 30);
        $gridGalleryObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-grid-gallery-obj-basic',
                    'label' => 'Grid Gallery'
                )
            )
        ));

/* POSTS SLIDER */
        $postsSliderObj = new MPCEObject(MPCEShortcode::PREFIX . 'posts_slider', __("Posts Slider", 'motopress-content-editor'), 'post-slider.png', array(
            'post_type' => array(
                'type' => 'select',
                'label' => __("Post types", 'motopress-content-editor'),
                'default' => 'post',
                'list' =>MPCEShortcode::getPostTypes(true), // true to get pages
            ),
            'category' => array(
                'type' => 'text',
                'label' => __("Display posts by category slug", 'motopress-content-editor'),
                'description' => __("Separate with ',' to display posts that have either of these categories or with '+' to display posts that have all of these categories.", 'motopress-content-editor'),
				'dependency' => array(
                    'parameter' => 'post_type',
                    'value' => 'post'
                ),
            ),
            'tag' => array(
                'type' => 'text',
                'label' => __("Display posts by tag slug", 'motopress-content-editor'),
                'description' => __("Separate with ',' to display posts that have either of these tags or with '+' to display posts that have all of these tags.", 'motopress-content-editor'),
				'dependency' => array(
                    'parameter' => 'post_type',
                    'value' => 'post'
                ),
            ),
            'custom_tax' => array(
                'type' => 'text',
                'label' => __("Custom Taxonomy", 'motopress-content-editor'),
                'default' => ''
            ),
            'custom_tax_field' => array(
                'type' => 'select',
                'label' => __("Taxonomy field", 'motopress-content-editor'),
                'default' => 'slug',
                'list' => array(
                    'term_id' => __("Term ID", 'motopress-content-editor'),
                    'slug' => __("Slug", 'motopress-content-editor'),
                    'name' => __("Name", 'motopress-content-editor')
                )
            ),
            'custom_tax_terms' => array(
                'type' => 'text',
                'label' => __("Taxonomy term(s)", 'motopress-content-editor'),
                'default' => '',
                'description' =>__("Separate with ',' to display posts that have either of these terms or with '+' to display posts that have all of these tags.", 'motopress-content-editor')
            ),
            'posts_count' => array(
                'type' => 'spinner',
                'label' => __("Posts count", 'motopress-content-editor'),
                'default' => 3,
				'min' => 1,
                'max' => 100,
                'step' => 1
            ),
            'order_by' => array(
                'type' => 'select',
                'label' => __("Order by", 'motopress-content-editor'),
                'default' => 'date',
                'list' => array(
                    'ID' => __("ID", 'motopress-content-editor'),
                    'date' => __("Date", 'motopress-content-editor'),
                    'author' => __("Author", 'motopress-content-editor'),
                    'modified' => __("Modified", 'motopress-content-editor'),
                    'rand' => __("Random", 'motopress-content-editor'),
                    'comment_count' => __("Comment count", 'motopress-content-editor'),
                    'menu_order' => __("Menu order", 'motopress-content-editor'),
                ),
            ),
            'sort_order' => array(
                'type' => 'radio-buttons',
                'label' => __("Sort order", 'motopress-content-editor'),
                'default' => 'DESC',
                'list' => array(
                    'ASC' => __("Ascending", 'motopress-content-editor'),
                    'DESC' => __("Descending", 'motopress-content-editor')
                ),
            ),
            'title_tag' => array(
                'type' => 'radio-buttons',
                'label' => __("Post title", 'motopress-content-editor'),
                'default' => 'h2',
                'list' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'hide' => __("Hide", 'motopress-content-editor'),
                )
            ),
            'show_content' => array(
                'type' => 'radio-buttons',
                'label' => __("Post description", 'motopress-content-editor'),
                'default' => 'short',
                'list' => array(
                    'short' => __("Short", 'motopress-content-editor'),
                    'full' => __("Full", 'motopress-content-editor'),
                    'excerpt' => __("Excerpt", 'motopress-content-editor'),
                    'hide' => __("None", 'motopress-content-editor'),
                ),
            ),
            'short_content_length' => array(
                'type' => 'slider',
                'label' => __("Length of the Text", 'motopress-content-editor'),
                'default' => 200,
                'min' => 0,
                'max' => 1000,
                'step' => 20,
                'dependency' => array(
                    'parameter' => 'show_content',
                    'value' => 'short'
                ),
            ),
            'image_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Image size", 'motopress-content-editor'),
                'default' => 'thumbnail',
                'list' => array(
                    'full' => __("Full", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'medium' => __("Medium", 'motopress-content-editor'),
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                ),
				'dependency' => array(
					'parameter' => 'layout',
					'except' => 'title_text'
				)
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => __("Image size in pixels, ex. 200x100 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'image_size',
                    'value' => 'custom'
                ),
            ),
            'layout' => array(
                'type' => 'select',
                'label' => __("Content style", 'motopress-content-editor'),
                'default' => 'title_img_text_wrap',
                'list' => array(
                    //'title_img_text' => $motopressCELang->CEPostsSliderLayoutTitleImageText,
                    'img_title_text' => __("Image, Title, Text", 'motopress-content-editor'),
                    //'title_img_inline' => $motopressCELang->CEPostsSliderLayoutImageTitleInline,
                    'title_img_text_wrap' => __("Title, Image and Text", 'motopress-content-editor'),
					'title_text'=> __("Title, Text", 'motopress-content-editor')
                ),
            ),
            'img_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Image position", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor'),
                ),
				'dependency' => array(
					'parameter' => 'layout',
					'except' => 'title_text'
				)
            ),
            'post_link' => array(
                'type' => 'select',
                'label' => __("Link to", 'motopress-content-editor'),
                'default' => 'link_to_post',
                'list' => array(
                    'link_to_post' => __("Original post", 'motopress-content-editor'),
                    'custom_link' => __("Custom links", 'motopress-content-editor'),
                    'no_link' => __("None", 'motopress-content-editor'),
                ),
            ),
            'custom_links' => array(
                'type' => 'longtext',
                'label' => __("Custom links", 'motopress-content-editor'),
                'default' => site_url(),
                'description' => __("Enter links for each slide here. Divide links with linebreaks (Enter).", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'post_link',
                    'value' => 'custom_link'
                ),
            ),
            'slideshow_speed' => array(
                'type' => 'radio-buttons',
                'label' => __("Auto rotate (s)", 'motopress-content-editor'),
                'default' => '15000',
                'list' => array(
                    '3000' => '3',
                    '5000' => '5',
                    '10000' => '10',
                    '15000' => '15',
                    '25000' => '25',
                    'disable' => __("Disable", 'motopress-content-editor'),
                ),
            ),
            'animation' => array(
                'type' => 'select',
                'label' => __("Animation type", 'motopress-content-editor'),
                'default' => 'fade',
                'list' => array(
                    'slide' => __("Slide", 'motopress-content-editor'),
                    'fade' => __("Fade", 'motopress-content-editor'),
                ),
            ),
            'smooth_height' => array(
                'type' => 'checkbox',
                'label' => __("Smooth height", 'motopress-content-editor'),
                'default' => 'true',
                'description' => __("Animate the height of the slider smoothly for slides of varying height", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'animation',
                    'value' => 'slide'
                )
            ),
            'show_nav' => array(
                'type' => 'checkbox',
                'label' => __("Show bullets", 'motopress-content-editor'),
                'default' => 'true',
            ),
            'pause_on_hover' => array(
                'type' => 'checkbox',
                'label' => __("Pause on hover", 'motopress-content-editor'),
                'default' => 'true',
            ),
        ), 35);

/* IMAGE SLIDER */
        $imageSlider = new MPCEObject(MPCEShortcode::PREFIX . 'image_slider', __("Slider", 'motopress-content-editor'), 'image-slider.png', array(
            'ids' => array(
                'type' => 'multi-images',
                'label' => __("Edit Slides", 'motopress-content-editor'),
                'default' => '',
                'description' => __("Select images from Media Library", 'motopress-content-editor'),
                'text' => __("Organize Images", 'motopress-content-editor'),
                'autoOpen' => 'true'
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => __("Image size", 'motopress-content-editor'),
                'default' => 'full',
                'list' => array(
                    'full' => __("Full", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'medium' => __("Medium", 'motopress-content-editor'),
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                )
            ),
            'custom_size' => array(
                'type' => 'text',
                'description' => __("Image size in pixels, ex. 200x100 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'size',
                    'value' => 'custom'
                ),
            ),
            'animation' => array(
                'type' => 'radio-buttons',
                'label' => __("Animation type", 'motopress-content-editor'),
                'default' => 'fade',
                'description' => __("Preview the page to view the animation", 'motopress-content-editor'),
                'list' => array(
                    'fade' => __("Fade", 'motopress-content-editor'),
                    'slide' => __("Slide", 'motopress-content-editor')
                )
            ),
            'smooth_height' => array(
                'type' => 'checkbox',
                'label' => __("Smooth height", 'motopress-content-editor'),
                'default' => 'false',
                'description' => __("Animate the height of the slider smoothly for slides of varying height", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'animation',
                    'value' => 'slide'
                )
            ),
            'slideshow' => array(
                'type' => 'checkbox',
                'label' => __("Enable slideshow", 'motopress-content-editor'),
                'default' => 'true',
                'description' => __("The slideshow will start automatically when the page is loaded", 'motopress-content-editor')
            ),
            'slideshow_speed' => array(
                'type' => 'slider',
                'label' => __("Slideshow speed in seconds", 'motopress-content-editor'),
                'default' => 7,
                'min' => 1,
                'max' => 20,
                'dependency' => array(
                    'parameter' => 'slideshow',
                    'value' => 'true'
                )
            ),
            'animation_speed' => array(
                'type' => 'slider',
                'label' => __("Animation speed in milliseconds", 'motopress-content-editor'),
                'default' => 600,
                'min' => 200,
                'max' => 10000,
                'step' => 200
            ),
            'control_nav' => array(
                'type' => 'checkbox',
                'label' => __("Show bullets", 'motopress-content-editor'),
                'default' => 'true'
            )
        ), 20);
        $imageSlider->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> ul.slides'
            ),
//			'mp_custom_style' => array(
//				'selector' => '> ul.slides'
//			)
        ));

/* BUTTON */
	    $buttonParameters = array(
            'text' => array(
                'type' => 'text',
                'label' => __("Text on the button", 'motopress-content-editor'),
                'default' => __("Button", 'motopress-content-editor')
            ),
            'link' => array(
                'type' => 'link',
                'label' => __("Link", 'motopress-content-editor'),
                'default' => '#',
                'description' => __("ex. http://yoursite.com/ or /blog", 'motopress-content-editor')
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => __("Open link in new window (tab)", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'none',
                'list' => $this->getIconClassList(true)
            ),
            'icon_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                ),
            ),
            'full_width' => array(
                'type' => 'checkbox',
                'label' => __("Stretch", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'full_width',
                    'value' => 'false'
                )
            ),
        );
		
        $buttonStyles = array(			
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-btn',
                    'label' => __("Button", 'motopress-content-editor')
                ),
                'predefined' => array(
                    'color' => array(
                        'label' => __("Button color", 'motopress-content-editor'),
                        'values' => array(
                            'silver' => array(
                                'class' => 'motopress-btn-color-silver',
                                'label' => __("Silver", 'motopress-content-editor')
                            ),
                            'red' => array(
                                'class' => 'motopress-btn-color-red',
                                'label' => __("Red", 'motopress-content-editor')
                            ),
                            'pink-dreams' => array(
                                'class' => 'motopress-btn-color-pink-dreams',
                                'label' => __("Pink Dreams", 'motopress-content-editor')
                            ),
                            'warm' => array(
                                'class' => 'motopress-btn-color-warm',
                                'label' => __("Warm", 'motopress-content-editor')
                            ),
                            'hot-summer' => array(
                                'class' => 'motopress-btn-color-hot-summer',
                                'label' => __("Hot Summer", 'motopress-content-editor')
                            ),
                            'olive-garden' => array(
                                'class' => 'motopress-btn-color-olive-garden',
                                'label' => __("Olive Garden", 'motopress-content-editor')
                            ),
                            'green-grass' => array(
                                'class' => 'motopress-btn-color-green-grass',
                                'label' => __("Green Grass", 'motopress-content-editor')
                            ),
                            'skyline' => array(
                                'class' => 'motopress-btn-color-skyline',
                                'label' => __("Skyline", 'motopress-content-editor')
                            ),
                            'aqua-blue' => array(
                                'class' => 'motopress-btn-color-aqua-blue',
                                'label' => __("Aqua Blue", 'motopress-content-editor')
                            ),
                            'violet' => array(
                                'class' => 'motopress-btn-color-violet',
                                'label' => __("Violet", 'motopress-content-editor')
                            ),
                            'dark-grey' => array(
                                'class' => 'motopress-btn-color-dark-grey',
                                'label' => __("Dark Grey", 'motopress-content-editor')
                            ),
                            'black' => array(
                                'class' => 'motopress-btn-color-black',
                                'label' => __("Black", 'motopress-content-editor')
                            )
                        )
                    ),
                    'size' => array(
                        'label' => __("Size", 'motopress-content-editor'),
                        'values' => array(
                            'mini' => array(
                                'class' => 'motopress-btn-size-mini',
                                'label' => __("Mini", 'motopress-content-editor')
                            ),
                            'small' => array(
                                'class' => 'motopress-btn-size-small',
                                'label' => __("Small", 'motopress-content-editor')
                            ),
                            'middle' => array(
                                'class' => 'motopress-btn-size-middle',
                                'label' => __("Middle", 'motopress-content-editor')
                            ),
                            'large' => array(
                                'class' => 'motopress-btn-size-large',
                                'label' => __("Large", 'motopress-content-editor')
                            )
                        )
                    ),
                    'icon indent' => array(
                        'label' => __("Icon indent", 'motopress-content-editor'),
                        'values' => array(
                            'mini' => array(
                                'class' => 'motopress-btn-icon-indent-mini',
                                'label' => __("Mini", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor')
                            ),
                            'small' => array(
                                'class' => 'motopress-btn-icon-indent-small',
	                            'label' => __("Small", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor')
                            ),
                            'middle' => array(
                                'class' => 'motopress-btn-icon-indent-middle',
	                            'label' => __("Middle", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor')
                            ),
                            'large' => array(
                                'class' => 'motopress-btn-icon-indent-large',
	                            'label' => __("Large", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor')
                            )
                        ),
                    ),
                    'rounded' => array(
                        'class' => 'motopress-btn-rounded',
                        'label' => __("Rounded", 'motopress-content-editor')
                    )
                ),
                'default' => array('motopress-btn-color-silver', 'motopress-btn-size-middle', 'motopress-btn-rounded', 'motopress-btn-icon-indent-small'),
                'selector' => '> a'
            ),
			'mp_custom_style' => array(
				'selector' => '> a'
			)
        );

        $buttonObj = new MPCEObject(MPCEShortcode::PREFIX . 'button', __("Button", 'motopress-content-editor'), 'button.png', $buttonParameters, 10);
        $buttonObj->addStyle($buttonStyles);

	    
	   
/* DOWNLOAD BUTTON*/        
        $downloadButtonObj = new MPCEObject(MPCEShortcode::PREFIX . 'download_button', __("Download Button", 'motopress-content-editor'), 'download-button.png', array(
			'attachment' => array(
                'type' => 'media',
                'returnMode' => 'id', // url or id
                'label' => __("Media File", 'motopress-content-editor'),
                'description' => __("Select file from Media Library", 'motopress-content-editor'),
                'default' => '',
            ),
			'text' => array(
                'type' => 'text',
                'label' => __("Text on the button", 'motopress-content-editor'),
                'default' => 'Download'
            ),
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'fa fa-download',
                'list' => $this->getIconClassList(true)
            ),
            'icon_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
            ),
            'full_width' => array(
                'type' => 'checkbox',
                'label' => __("Stretch", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'full_width',
                    'value' => 'false'
                )
            )
		), 30);
		
	    $downloadButtonObj->addStyle($buttonStyles);


/* ICON */
        $iconObj = new MPCEObject(MPCEShortcode::PREFIX . 'icon', __("Icon", 'motopress-content-editor'), 'icon.png', array(
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'fa fa-star',
                'list' => $this->getIconClassList()
            ),
            'icon_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon color", 'motopress-content-editor'),
                'default' => '#e6cf03',
            ),
            'icon_size' => array(
                'type' => 'select',
                'label' => __("Size", 'motopress-content-editor'),
                'default' => 'large',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor'),
                    'middle' => __("Middle", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'extra-large' => __("Extra Large", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                ),
            ),
            'icon_size_custom' => array(
		        'type' => 'spinner',
		        'label' => __("Icon custom size", 'motopress-content-editor'),
		        'description' => __("Font size in px", 'motopress-content-editor'),
		        'min' => 1,
		        'step' => 1,
		        'max' => 500,
		        'default' => 26,
		        'dependency' => array(
			        'parameter' => 'icon_size',
			        'value' => 'custom'
		        )
	        ),   
            'icon_alignment' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon alignment", 'motopress-content-editor'),
                'default' => 'center',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor'),
                ),
            ),
            'bg_shape' => array(
                'type' => 'select',
                'label' => __("Background shape", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'circle' => __("Circle", 'motopress-content-editor'),
                    'square' => __("Square", 'motopress-content-editor'),
                    'rounded' => __("Rounded", 'motopress-content-editor'),
                    'outline-circle' => __("Outline Circle", 'motopress-content-editor'),
                    'outline-square' => __("Outline Square", 'motopress-content-editor'),
                    'outline-rounded' => __("Outline Rounded", 'motopress-content-editor'),
                ),
            ),
             'icon_background_size' => array(
                'type' => 'spinner',
                'label' => __("Icon background size", 'motopress-content-editor'),
                'default' => 1.5,
                'min' => 1,
                'max' => 3,
                'step' => 0.1,
                'dependency' => array(
                    'parameter' => 'bg_shape',
                    'except' => 'none'
                )
            ),
            'bg_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon background color", 'motopress-content-editor'),
                'default' => '#42414f',
                'dependency' => array(
                    'parameter' => 'bg_shape',
                    'except' => 'none'
                ),
            ),
            'animation' => array(
                'type' => 'select',
                'label' => __("Appearance effect", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top-to-bottom' => __("Top to bottom", 'motopress-content-editor'),
                    'bottom-to-top' => __("Bottom to top", 'motopress-content-editor'),
                    'left-to-right' => __("Left to right", 'motopress-content-editor'),
                    'right-to-left' => __("Right to left", 'motopress-content-editor'),
                    'appear' => __("Appear", 'motopress-content-editor'),
                ),
            ),
            'link' => array(
                'type' => 'link',
                'label' => __("Link", 'motopress-content-editor'),
                'default' => ''
            ),
        ), 70);
		$iconObj->addStyle(array(
			'mp_custom_style' => array(
				'limitation' => 'padding'
			)
		));
           
/* COUNTDOWN TIMER */
       $countdownTimerObj = new MPCEObject(MPCEShortcode::PREFIX . 'countdown_timer', __("Countdown Timer", 'motopress-content-editor'), 'countdown-timer.png', array(

             'date' => array(
                'type' => 'datetime-picker',
                'displayMode' => 'datetime', // date | datetime (default)
                'returnMode' => 'YYYY-MM-DD H:m:s', // mysql format uses here (default: Y-m-d H:i:s )
                'label' => __("Expiration Date", 'motopress-content-editor'),
                'default' => '',
             ),
             'time_zone' => array(
                 'type' => 'select',
                 'label' => __("Time zone", 'motopress-content-editor'),
                'default' => 'server_time',
                'list' => array(
                    'server_time' => __("Server time", 'motopress-content-editor'),
                    'user_local' => __("User's local time", 'motopress-content-editor')
                ),
             ),
            'format' => array(
                'type' => 'text',
                'label' => __("Format", 'motopress-content-editor'),
                'default' => 'yowdHMS',
                'description' => __("Use 'Y' for years, 'O' for months, 'W' for weeks, 'D' for days, 'H' for hours, 'M' for minutes, 'S' for seconds. Upper-case characters for required fields and lower-case characters for display only if non-zero.", 'motopress-content-editor')
            ),
            'block_color' => array(
                'type' => 'color-picker',
                'label' => __("Background Color", 'motopress-content-editor'),
                'default' => '#333333',
            ),
            'font_color' => array(
                'type' => 'color-picker',
                'label' => __("Text Color", 'motopress-content-editor'),
                'default' => '#ffffff',
            ),
            'blocks_size' => array(
		        'type' => 'spinner',
		        'label' => __("Block size", 'motopress-content-editor'),
		        'min' => 1,
		        'step' => 1,
		        'max' => 480,
		        'default' => 60,
	        ),
            'digits_font_size' => array(
		        'type' => 'slider',
		        'label' => __("Digits size", 'motopress-content-editor'),
		        'min' => 8,
		        'step' => 1,
		        'max' => 300,
		        'default' => 36
	        ),
            'labels_font_size' => array(
		        'type' => 'slider',
		        'label' => __("Text size", 'motopress-content-editor'),
		        'min' => 6,
		        'step' => 1,
		        'max' => 96,
		        'default' => 12
	        ),
            'blocks_space' => array(
		        'type' => 'spinner',
		        'label' => __("Spacing", 'motopress-content-editor'),
		        'min' => 0,
		        'step' => 1,
		        'max' => 160,
		        'default' => 10,
	        ),
         ), 70);
       

/* ACCORDION */
        $accordionObj = new MPCEObject(MPCEShortcode::PREFIX . 'accordion', __("Accordion", 'motopress-content-editor'), 'accordion.png', array(
            'elements' => array(
                'type' => 'group',
                'contains' => MPCEShortcode::PREFIX . 'accordion_item',
                'items' => array(
                    'label' => array(
                        'default' => __("Section title", 'motopress-content-editor'),
                        'parameter' => 'title'
                    ),
                    'count' => 2
                ),
                'text' => sprintf(__("Add new %s item", 'motopress-content-editor'), __("Accordion", 'motopress-content-editor')),
                'activeParameter' => 'active',
                'rules' => array(
                    'rootSelector' => '.motopress-accordion-item',
                    'activeSelector' => '> h3',
                    'activeClass' => 'ui-state-active'
                ),
                'events' => array(
                    'onActive' => array(
                        'selector' => '> h3',
                        'event' => 'click'
                    ),
                    'onInactive' => array(
                        'selector' => '> h3',
                        'event' => 'click'
                    )
                )
            ),
        ), 25, MPCEObject::ENCLOSED);
        $accordionObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-accordion',
                    'label' => __("Accordion", 'motopress-content-editor')
                ),
                'predefined' => array(
                    'style' => array(
                        'label' => __("Style", 'motopress-content-editor'),
                        'values' => array(
                            'light' => array(
                                'class' => 'motopress-accordion-light',
                                'label' => __("Light", 'motopress-content-editor')
                            ),
                            'dark' => array(
                                'class' => 'motopress-accordion-dark',
                                'label' => __("Dark", 'motopress-content-editor')
                            )
                        )
                    )
                ),
                'default' => array('motopress-accordion-light')
            )
        ));

        $accordionItemObj = new MPCEObject(MPCEShortcode::PREFIX . 'accordion_item', __("Accordion Section", 'motopress-content-editor'), null, array(
            'title' => array(
                'type' => 'text',
                'label' => __("Section title", 'motopress-content-editor'),
                'default' => __("Section title", 'motopress-content-editor')
            ),
            'content' => array(
                'type' => 'longtext-tinymce',
                'label' => __("Section content", 'motopress-content-editor'),
                'default' => __("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam eu hendrerit nunc. Proin tempus pulvinar augue, quis ultrices urna consectetur non.", 'motopress-content-editor'),
                'text' => __("Open in WordPress Editor", 'motopress-content-editor'),
                'saveInContent' => 'true'
            ),
            'active' => array(
                'type' => 'group-checkbox',
                'label' => __("Active", 'motopress-content-editor'),
                'default' => 'false',
                'description' => sprintf(__("Only one %s can be active at a time", 'motopress-content-editor'), __("Accordion Section", 'motopress-content-editor'))
            )
        ), null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE, false);

/* TABS */
        $tabsObj = new MPCEObject(MPCEShortcode::PREFIX . 'tabs', __("Tabs", 'motopress-content-editor'), 'tabs.png', array(
            'elements' => array(
                'type' => 'group',
                'contains' => MPCEShortcode::PREFIX . 'tab',
                'items' => array(
                    'label' => array(
                        'default' => __("Tab title", 'motopress-content-editor'),
                        'parameter' => 'title'
                    ),
                    'count' => 2
                ),
                'text' => sprintf(__("Add new %s item", 'motopress-content-editor'), __("Tab", 'motopress-content-editor')),
	            'activeParameter' => 'active',
                'rules' => array(
                    'rootSelector' => '.ui-tabs-nav > li',
                    'activeSelector' => '',
                    'activeClass' => 'ui-state-active'
                ),
                'events' => array(
                    'onActive' => array(
                        'selector' => '> a',
                        'event' => 'click'
                    )
                ),
            ),
            'padding' => array(
                'type' => 'slider',
                'label' => __("Space between borders and tab content", 'motopress-content-editor'),
                'default' => 20,
                'min' => 0,
                'max' => 50,
                'step' => 10
            ),
            'vertical' => array(
                'type' => 'checkbox',
                'label' => __("Vertical Tabs", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'rotate' => array(
                'type' => 'radio-buttons',
                'label' => __("Auto rotate (s)", 'motopress-content-editor'),
                'default' => 'disable',
                'list' => array(
                    'disable' => __("Disable", 'motopress-content-editor'),
                    '3000' => '3',
                    '5000' => '5',
                    '10000' => '10',
                    '15000' => '15',
                )
            ),
        ), 20, MPCEObject::ENCLOSED);
        $tabsObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-tabs-basic',
                    'label' => __("Tabs", 'motopress-content-editor')
                ),
                'predefined' => array(
                    'style' => array(
                        'label' => __("Navigation", 'motopress-content-editor'),
                        'values' => array(
                            'full-width' => array(
                                'class' => 'motopress-tabs-fullwidth',
                                'label' => __("Full width navigation", 'motopress-content-editor')
                            )
                        )
                    ),
                ),
                'selector' => ''
            )
        ));

        $tabObj = new MPCEObject(MPCEShortcode::PREFIX . 'tab', __("Tab", 'motopress-content-editor'), null, array(
            'id' => array(
                'type' => 'text-hidden',
	            'unique' => true
            ),
            'title' => array(
                'type' => 'text',
                'label' => __("Tab title", 'motopress-content-editor'),
                'default' => __("Tab title", 'motopress-content-editor')
            ),
            'content' => array(
                'type' => 'longtext-tinymce',
                'label' => __("Tab content", 'motopress-content-editor'),
                'default' => __("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam eu hendrerit nunc. Proin tempus pulvinar augue, quis ultrices urna consectetur non.", 'motopress-content-editor'),
                'text' => __("Open in WordPress Editor", 'motopress-content-editor'),
                'saveInContent' => 'true'
            ),
	        'icon' => array(
		        'type' => 'icon-picker',
		        'label' => __("Icon", 'motopress-content-editor'),
		        'default' => 'none',
		        'list' => $this->getIconClassList(true)
	        ),
	        'icon_size' => array(
		        'type' => 'radio-buttons',
		        'label' => __("Icon size", 'motopress-content-editor'),
		        'default' => 'normal',
		        'list' => array(
			        'normal' => __("Normal", 'motopress-content-editor'),
			        'custom' => __("Custom", 'motopress-content-editor'),
		        ),
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'icon_custom_size' => array(
		        'type' => 'spinner',
		        'label' => __("Icon custom size", 'motopress-content-editor'),
		        'description' => __("Font size in px", 'motopress-content-editor'),
		        'min' => 1,
		        'step' => 1,
		        'max' => 500,
		        'default' => 26,
		        'dependency' => array(
			        'parameter' => 'icon_size',
			        'value' => 'custom'
		        )
	        ),
	        'icon_color' => array(
		        'type' => 'color-select',
		        'label' => __("Icon color", 'motopress-content-editor'),
		        'default' => 'inherit',
		        'list' => array(
					'inherit' => __("Inherit", 'motopress-content-editor'),
			        'mp-text-color-black' => __("Black", 'motopress-content-editor'),
			        'mp-text-color-red' => __("Red", 'motopress-content-editor'),
			        'mp-text-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
			        'mp-text-color-warm' => __("Warm", 'motopress-content-editor'),
			        'mp-text-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
			        'mp-text-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
			        'mp-text-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
			        'mp-text-color-skyline' => __("Skyline", 'motopress-content-editor'),
			        'mp-text-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
			        'mp-text-color-violet' => __("Violet", 'motopress-content-editor'),
			        'mp-text-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
			        'mp-text-color-default' => __("Silver", 'motopress-content-editor'),
			        'custom' => __("Custom", 'motopress-content-editor'),
		        ),
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'icon_custom_color' => array(
		        'type' => 'color-picker',
		        'label' => __("Icon custom color", 'motopress-content-editor'),
		        'default' => '#000000',
		        'dependency' => array(
			        'parameter' => 'icon_color',
			        'value' => 'custom'
		        )
	        ),
	        'icon_margin_left' => array(
		        'type' => 'spinner',
		        'label' => __("Icon margin Left", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 500,
		        'step' => 1,
		        'default' => '5',
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'icon_margin_right' => array(
		        'type' => 'spinner',
		        'label' => __("Icon margin Right", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 500,
		        'step' => 1,
		        'default' => '5',
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'icon_margin_top' => array(
		        'type' => 'spinner',
		        'label' => __("Icon margin Top", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 500,
		        'step' => 1,
		        'default' => '0',
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'icon_margin_bottom' => array(
		        'type' => 'spinner',
		        'label' => __("Icon margin Bottom", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 500,
		        'step' => 1,
		        'default' => '0',
		        'dependency' => array(
                    'parameter' => 'icon',
                    'except' => 'none'
                )
	        ),
	        'active' => array(
		        'type' => 'group-checkbox',
		        'label' => __("Active", 'motopress-content-editor'),
		        'default' => 'false',
		        'description' => sprintf(__("Only one %s can be active at a time", 'motopress-content-editor'), __("Tab", 'motopress-content-editor'))
	        )
        ), null, MPCEObject::ENCLOSED, MPCEObject::RESIZE_NONE, false);

/* SOCIAL BUTTONS */
        $socialsObj = new MPCEObject(MPCEShortcode::PREFIX . 'social_buttons', __("Social Share Buttons", 'motopress-content-editor'), 'social-buttons.png', array(
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'motopress-text-align-left',
                'list' => array(
                    'motopress-text-align-left' => __("Left", 'motopress-content-editor'),
                    'motopress-text-align-center' => __("Center", 'motopress-content-editor'),
                    'motopress-text-align-right' => __("Right", 'motopress-content-editor')
                )
            )
        ), 40, MPCEObject::ENCLOSED);
        $socialsObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => array(
                    'size' => array(
                        'label' => __("Size", 'motopress-content-editor'),
                        'values' => array(
                            'normal' => array(
                                'class' => 'motopress-buttons-32x32',
                                'label' => __("Middle", 'motopress-content-editor')
                            ),
                            'large' => array(
                                'class' => 'motopress-buttons-64x64',
                                'label' => __("Large", 'motopress-content-editor')
                            )
                        )
                    ),
                    'style' => array(
                        'label' => __("Style", 'motopress-content-editor'),
                        'values' => array(
                            'plain' => array(
                                'class' => 'motopress-buttons-square',
                                'label' => __("Plain", 'motopress-content-editor')
                            ),
                            'rounded' => array(
                                'class' => 'motopress-buttons-rounded',
                                'label' => __("Rounded", 'motopress-content-editor')
                            ),
                            'circular' => array(
                                'class' => 'motopress-buttons-circular',
                                'label' => __("Circular", 'motopress-content-editor')
                            ),
                            'volume' => array(
                                'class' => 'motopress-buttons-volume',
                                'label' => __("Volume", 'motopress-content-editor')
                            )
                        )
                    )
                ),
                'default' => array('motopress-buttons-32x32', 'motopress-buttons-square')
            )
        ));

/* SOCIAL PROFILE/LINKS */
        $socialProfileObj = new MPCEObject(MPCEShortcode::PREFIX . 'social_profile', __("Social Buttons", 'motopress-content-editor'), 'social-profile.png', array(
            'facebook' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Facebook'),
                'default' => 'https://www.facebook.com/motopressapp'
            ),
            'google' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Google+'),
                'default' => 'https://plus.google.com/+Getmotopress/posts'
            ),
            'twitter' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Twitter'),
                'default' => 'https://twitter.com/motopressapp'
            ),
            'pinterest' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Pinterest'),
                'default' => 'http://www.pinterest.com/motopress/'
            ),
            'linkedin' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'LinkedIn'),
            ),
            'flickr' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Flickr'),
            ),
            'vk' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'VK'),
            ),
            'delicious' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Delicious'),
            ),
            'youtube' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'YouTube'),
                'default' => 'https://www.youtube.com/channel/UCtkDYmIQ5Lv_z8KbjJ2lpFQ'
            ),
            'rss' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'RSS'),
                'default' => 'https://motopress.com/feed/'
            ),
            'instagram' => array(
                'type' => 'text',
                'label' => sprintf(__("%s URL", 'motopress-content-editor'),  'Instagram'),
                'default' => ''
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            )
        ), 50);
        $socialProfileObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => array(
                    'size' => array(
                        'label' => __("Size", 'motopress-content-editor'),
                        'values' => array(
                            'normal' => array(
                                'class' => 'motopress-buttons-32x32',
                                'label' => __("Middle", 'motopress-content-editor')
                            ),
                            'large' => array(
                                'class' => 'motopress-buttons-64x64',
                                'label' => __("Large", 'motopress-content-editor')
                            )
                        )
                    ),
                    'style' => array(
                        'label' => __("Style", 'motopress-content-editor'),
                        'values' => array(
                            'plain' => array(
                                'class' => 'motopress-buttons-square',
                                'label' => __("Plain", 'motopress-content-editor')
                            ),
                            'rounded' => array(
                                'class' => 'motopress-buttons-rounded',
                                'label' => __("Rounded", 'motopress-content-editor')
                            ),
                            'circular' => array(
                                'class' => 'motopress-buttons-circular',
                                'label' => __("Circular", 'motopress-content-editor')
                            ),
                            'volume' => array(
                                'class' => 'motopress-buttons-volume',
                                'label' => __("Volume", 'motopress-content-editor')
                            )
                        )
                    )
                ),
                'default' => array('motopress-buttons-32x32', 'motopress-buttons-square')
            )
        ));


/* VIDEO */
        $videoObj = new MPCEObject(MPCEShortcode::PREFIX . 'video', __("Video", 'motopress-content-editor'), 'video.png', array(
            'src' => array(
                'type' => 'video',
                'label' => __("Video URL", 'motopress-content-editor'),
                'default' => MPCEShortcode::DEFAULT_VIDEO,
                'description' => __("Paste the URL of a video (Vimeo or YouTube) you'd like to embed", 'motopress-content-editor')
            )
        ), 10);
        $videoObj->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> iframe'
            ),
			'mp_custom_style' => array(
				'selector' => '> iframe',
				'limitation' => array(
					'margin'
				)
			)
        ));

/* AUDIO */
         $wpAudioObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_audio', __("Audio", 'motopress-content-editor'), 'player.png', array(
            'source' => array(
                'type' => 'select',
                'label' => __("Audio source", 'motopress-content-editor'),
                'description' => __("If your current browser does not support HTML5 audio or Flash Player is not installed, a direct download link will be displayed instead of the player", 'motopress-content-editor'),
                'list' => array(
                    'library' => __("Media Library", 'motopress-content-editor'),
                    'external' => __("Audio file URL", 'motopress-content-editor'),
                ),
                'default' => 'external'
            ),
            'id' => array(
                'type' => 'audio',
                'label' => __("Audio File", 'motopress-content-editor'),
                'description' => __("Select audio file from Media Library", 'motopress-content-editor'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'source',
                    'value' => 'library'
                )
                ),
            'url' => array(
                'type' => 'text',
                'label' => __("Audio file URL", 'motopress-content-editor'),
                'description' => __("Supported formats: .mp3, .m4a, .ogg, .wav", 'motopress-content-editor'),
                'default' => '//wpcom.files.wordpress.com/2007/01/mattmullenweg-interview.mp3',
                'dependency' => array(
                    'parameter' => 'source',
                    'value' => 'external'
                )
            ),
            'autoplay' => array(
                'type' => 'checkbox',
                'label' => __("Autoplay", 'motopress-content-editor'),
                'description' => __("Play file automatically when page is loaded", 'motopress-content-editor'),
                'default' => '',
            ),
            'loop' => array(
                'type' => 'checkbox',
                'label' => __("Repeat", 'motopress-content-editor'),
                'description' => __("Repeat when playback is ended", 'motopress-content-editor'),
                'default' => '',
            )
        ), 20, MPCEObject::ENCLOSED);

/* GOOGLE MAPS */
        $gMapObj = new MPCEObject(MPCEShortcode::PREFIX.'gmap', __("Google Maps", 'motopress-content-editor'), 'map.png', array(
            'address' => array(
                'type' => 'text',
                'label' => __("Address", 'motopress-content-editor'),
                'default' => 'Sydney, New South Wales, Australia',
                'description' => __("To find a specific address or location, just enter what you're looking for and press Enter", 'motopress-content-editor')
            ),
            'zoom' => array(
                'type' => 'slider',
                'label' => __("Zoom", 'motopress-content-editor'),
                'default' => 13,
                'min' => 0,
                'max' => 20
            ),
	        'min_height' => array(
		        'type' => 'spinner',
		        'label' => __("Min Height", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 10000,
		        'step' => 1,
		        'default' => '0',
	        )
        ), 65, null, MPCEObject::RESIZE_ALL);
        $gMapObj->addStyle(array(
            'mp_style_classes' => array(
                'selector' => '> iframe'
            ),
			'mp_custom_style' => array(
				'selector' => '> iframe'
			)
        ));

/* SPACE */
        $spaceObj = new MPCEObject(MPCEShortcode::PREFIX . 'space', __("Space", 'motopress-content-editor'), 'space.png', array(
	        'min_height' => array(
		        'type' => 'spinner',
		        'label' => __("Min Height", 'motopress-content-editor'),
		        'min' => 0,
		        'max' => 10000,
		        'step' => 1,
		        'default' => '0',
	        )
        ), 60, null, MPCEObject::RESIZE_ALL);
        $spaceObj->addStyle(array(
            'mp_style_classes' => array(
                'predefined' => $spacePredefinedStyles
            ),
			'mp_custom_style' => array(
				'limitation' => array('background', 'border', 'padding', 'margin-horizontal', 'color')
			)
        ));

/* EMBED */
        $embedObj = new MPCEObject(MPCEShortcode::PREFIX . 'embed', __("Embed", 'motopress-content-editor'), 'code.png', array(
            'data' => array(
                'type' => 'longtext64',
                'label' => __("Paste HTML code", 'motopress-content-editor'),
                'default' => 'PGk+UGFzdGUgeW91ciBjb2RlIGhlcmUuPC9pPg==',
                'description' => __("Note: Most &lt;script&gt; embeds will only appear on the published site. We recommend using &lt;iframe&gt; based embed code", 'motopress-content-editor')
            ),
            'fill_space' => array(
                'type' => 'checkbox',
                'label' => __("Fill available space", 'motopress-content-editor'),
                'default' => 'true',
                'description' => __("Expand object to fill available width and height", 'motopress-content-editor')
            )
        ), 75);

/* QUOTE */
        $quotesObj = new MPCEObject(MPCEShortcode::PREFIX . 'quote', __("Quote", 'motopress-content-editor'), 'quotes.png', array(
            'quote_content' => array(
                'type' => 'longtext',
                'label' => __("Quote", 'motopress-content-editor'),
                'default' => 'Lorem ipsum dolor sit amet.'
            ),
			'cite' => array(
                'type' => 'text',
                'label' => __("Cite", 'motopress-content-editor'),
                'default' => 'John Smith',
                'description' => __("Text representation of the source", 'motopress-content-editor'),
            ),
            'cite_url' => array(
                'type' => 'link',
                'label' => __("Cite URL", 'motopress-content-editor'),
                'default' => '',
                'description' => __("URL for the source of the quotation", 'motopress-content-editor'),
            )
            
        ), 40, MPCEObject::ENCLOSED);

/* MEMBERS CONTENT */
        $membersObj = new MPCEObject(MPCEShortcode::PREFIX . 'members_content', __("Members Content", 'motopress-content-editor'), 'members.png', array(
            'message' => array(
                'type' => 'text',
                'label' => __("Message for not logged in users", 'motopress-content-editor'),
                'default' => __("This content is for registered users only. Please %login%.", 'motopress-content-editor'),
                'description' => __("This message will see not logged in users", 'motopress-content-editor'),
            ),
            'login_text' => array(
                'type' => 'text',
                'label' => __("Login link text", 'motopress-content-editor'),
                'default' => __("login", 'motopress-content-editor'),
                'description' => __("Text for the login link", 'motopress-content-editor'),
            ),
            'members_content' => array(
                'type' => 'longtext-tinymce',
                'label' => __("Content for logged in users", 'motopress-content-editor'),
                'default' => __("Only registered users will see this content.", 'motopress-content-editor'),
	            'text' => __("Open in WordPress Editor", 'motopress-content-editor'),
	            'saveInContent' => 'true'
            ),
        ), 50, MPCEObject::ENCLOSED);

/* CHARTS */
        $googleChartsObj = new MPCEObject(MPCEShortcode::PREFIX . 'google_chart', __("Chart", 'motopress-content-editor'), 'chart.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => 'Company Performance'
            ),
            'type' => array(
                'type' => 'select',
                'label' => __("Chart type", 'motopress-content-editor'),
                'description' => __("Find out more about chart types at <a href='https://developers.google.com/chart/' target='_blank'>Google Charts</a>", 'motopress-content-editor'),
                'default' => 'ColumnChart',
                'list' => array(
                    'ColumnChart' => __("Column Chart", 'motopress-content-editor'),
                    'BarChart' => __("Bar Chart", 'motopress-content-editor'),
                    'AreaChart' => __("Area Chart", 'motopress-content-editor'),
                    'SteppedAreaChart' => __("Stepped Area Chart", 'motopress-content-editor'),
                    'PieChart' => __("Pie Chart", 'motopress-content-editor'),
                    'PieChart3D' => __("3D Pie Chart", 'motopress-content-editor'),
                    'LineChart' => __("Line Chart", 'motopress-content-editor'),
                    'Histogram' => __("Histogram", 'motopress-content-editor')
                )
            ),
            'donut' => array(
                'type' => 'checkbox',
                'label' => __("Donut Hole", 'motopress-content-editor'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'type',
                    'value' =>'PieChart'
                )
            ),
            'colors' => array(
                'type' => 'text',
                'label' => __("Chart colors", 'motopress-content-editor'),
                'description' => __("Comma separated HEX color values. Ex: #e0440e, #e6693e", 'motopress-content-editor'),
            ),
            'transparency' => array(
                'type' => 'checkbox',
                'label' => __("Transparent background", 'motopress-content-editor'),
                'default' => 'false',
            ),
            'table' => array(
                'type' => 'longtext-table',
                'label' => __("Data", 'motopress-content-editor'),
                'description' => __("Data in each row separated by comma", 'motopress-content-editor'),
                'default' => 'Year,Sales,Expenses<br />2004,1000,400<br />2005,1170,460<br />2006,660,1120<br />2007,1030,540',
                'saveInContent' => 'true'
            ),
            'min_height' => array(
	            'type' => 'spinner',
	            'label' => __("Min Height", 'motopress-content-editor'),
	            'min' => 0,
	            'max' => 10000,
	            'step' => 1,
	            'default' => '0',
            )
        ), 80, MPCEObject::ENCLOSED, MPCEObject::RESIZE_ALL);

/* TABLE */
        $tableObj = new MPCEObject(MPCEShortcode::PREFIX . 'table', __("Table", 'motopress-content-editor'), 'table.png', array(
            'table' => array(
                'type' => 'longtext-table',
                'label' => __("Data", 'motopress-content-editor'),
                'default' => 'Year,Sales,Expenses<br />2004,1000,400<br />2005,1170,460<br />2006,660,1120<br />2007,1030,540',
                'description' => __("Data in each row separated by comma. Find out more about <a href='http://en.wikipedia.org/wiki/Comma-separated_values' target='_blank'>CSV format</a>.", 'motopress-content-editor'),
                'saveInContent' => 'true'
            )
        ), 30, MPCEObject::ENCLOSED);
        $tableObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-table',
                    'label' => __("Table", 'motopress-content-editor')
                ),
                'predefined' => array(
                    'style' => array(
                        'label' => __("Style", 'motopress-content-editor'),
                        'allowMultiple' => true,
                        'values' => array(
                            'silver' => array(
                                'class' => 'motopress-table-style-silver',
                                'label' => __("Light", 'motopress-content-editor')
                            ),
                            'left' => array(
                                'class' => 'motopress-table-first-col-left',
                                'label' => __("First column left", 'motopress-content-editor')
                            )
                        )
                    )
                ),
                'default' => array('motopress-table-style-silver', 'motopress-table-first-col-left'),
                'selector' => '> table'
            ),
			'mp_custom_style' => array(
				'selector' => '> table',
				'limitation' => array('padding')
			)
        ));

/* POSTS GRID */
        $postsGridObj = new MPCEObject(MPCEShortcode::PREFIX . 'posts_grid', __("Posts Grid", 'motopress-content-editor'), 'posts-grid.png', array(
            'query_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Query Type", 'motopress-content-editor'),
                'description' => __("Choose query type", 'motopress-content-editor'),
                'default' => 'simple',
                'list' => array(
                    'simple' => __("Simple", 'motopress-content-editor'),
                    'custom' => __("Custom query", 'motopress-content-editor'),
                    'ids' => __("IDs", 'motopress-content-editor'),
                )
            ),
            'post_type' => array(
                'type' => 'select',
                'label' => __("Post Type to show", 'motopress-content-editor'),
                'description' => __("Select post type to populate posts from", 'motopress-content-editor'),
                'list' =>MPCEShortcode::getPostTypes(false),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
            'category' => array(
                'type' => 'text',
                'label' => __("Display posts by category slug", 'motopress-content-editor'),
                'description' => __("Separate with ',' to display posts that have either of these categories or with '+' to display posts that have all of these categories.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'post_type',
                    'value' => 'post'
                )
            ),
            'tag' => array(
                'type' => 'text',
                'label' => __("Display posts by tag slug", 'motopress-content-editor'),
                'description' => __("Separate with ',' to display posts that have either of these tags or with '+' to display posts that have all of these tags.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'post_type',
                    'value' => 'post'
                )
            ),
            'custom_tax' => array(
                'type' => 'text',
                'label' => __("Custom Taxonomy", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
            'custom_tax_field' => array(
                'type' => 'select',
                'label' => __("Taxonomy field", 'motopress-content-editor'),
                'default' => 'slug',
                'list' => array(
                    'term_id' => __("Term ID", 'motopress-content-editor'),
                    'slug' => __("Slug", 'motopress-content-editor'),
                    'name' => __("Name", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
            'custom_tax_terms' => array(
                'type' => 'text',
                'label' => __("Taxonomy term(s)", 'motopress-content-editor'),
                'description' => __("Separate with ',' to display posts that have either of these terms or with '+' to display posts that have all of these tags.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
            'posts_per_page' => array(
                'type' => 'spinner',
                'label' => __("Posts count", 'motopress-content-editor'),
                'default' => 4,
                'min' => 1,
                'max' => 40,
                'step' => 1,
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
//			'show_sticky_posts' => array(
//				'type' => 'checkbox',
//				'label' => 'Show sticky posts',
//				'default' => 'false',
//				'dependency' => array(
//					'parameter' => 'query_type',
//					'value' => 'simple'
//				)
//			), 
            'posts_order' => array(
                'type' => 'radio-buttons',
                'label' => __("Sort order", 'motopress-content-editor'),
                'default' => 'DESC',
                'list' => array(
                    'ASC' => __("Ascending", 'motopress-content-editor'),
                    'DESC' => __("Descending", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'simple'
                )
            ),
            'custom_query' => array(
                'type' => 'longtext64',
                'label' => __("Custom query", 'motopress-content-editor'),
                'description' => __("Build custom query according to <a href=\"http://codex.wordpress.org/Function_Reference/query_posts\">WordPress Codex</a>. Example: post_type=portfolio&posts_per_page=5&orderby=title", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'custom'
                )
            ),
            'ids' => array(
                'type' => 'text',
                'label' => __("IDs of posts", 'motopress-content-editor'),
                'description' => __("Separate with ','", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'query_type',
                    'value' => 'ids'
                )
            ),
            'columns' => array(
                'type' => 'radio-buttons',
                'label' => __("Columns count", 'motopress-content-editor'),
                'default' => 2,
                'list' => array( 
                    1 => 1,
                    2 => 2,
                    3 => 3,
                    4 => 4,
                    6 => 6
                )
            ),
            'template' => array(
                'type' => 'select',
                'label' => __("Post Style", 'motopress-content-editor'),
                'list' => MPCEShortcode::getPostsGridTemplatesList(),
            ),
            'posts_gap' => array(
                'type' => 'slider',
                'label' => __("Vertical gap between posts", 'motopress-content-editor'),
                'default' => 30,
                'min' => 0,
                'max' => 100,
                'step' => 10,
            ),
            'show_featured_image' => array(
                'type' => 'checkbox',
                'label' => __("Show Featured Image", 'motopress-content-editor'),
                'default' => 'true',
            ),
            'image_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Image size", 'motopress-content-editor'),
                'default' => 'large',
                'list' => array(
                    'full' => __("Full", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'medium' => __("Medium", 'motopress-content-editor'),
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'show_featured_image',
                    'value' => 'true'
                ),
            ),
            'image_custom_size' => array(
                'type' => 'text',
                'description' => __("Image size in pixels, ex. 200x100 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'image_size',
                    'value' => 'custom'
                ),
            ),
            'title_tag' => array(
                'type' => 'radio-buttons',
                'label' => __("Title style", 'motopress-content-editor'),
                'default' => 'h2',
                'list' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'hide' => __("Hide", 'motopress-content-editor'),
                )
            ),
            'show_date_comments' => array(
                'type' => 'checkbox',
                'label' => __("Show Date and Comments", 'motopress-content-editor'),
                'default' => 'true',
            ),
            'show_content' => array(
                'type' => 'radio-buttons',
                'label' => __("Post description", 'motopress-content-editor'),
                'default' => 'short',
                'list' => array(
                    'short' => __("Short", 'motopress-content-editor'),
                    'full' => __("Full", 'motopress-content-editor'),
                    'excerpt' => __("Excerpt", 'motopress-content-editor'),
                    'hide' => __("None", 'motopress-content-editor'),
                )
            ),
            'short_content_length' => array(
                'type' => 'slider',
                'label' => __("Length of the Text", 'motopress-content-editor'),
                'default' => 200,
                'min' => 0,
                'max' => 1000,
                'step' => 20,
                'dependency' => array(
                    'parameter' => 'show_content',
                    'value' => 'short'
                ),
            ),
            'read_more_text' => array(
                'type' => 'text',
                'label' => __("Post Read More link text", 'motopress-content-editor'),
                'default' => __("Read more", 'motopress-content-editor')
            ),
            'display_style' => array(
                'type' => 'radio-buttons',
                'label' => __("Display Style", 'motopress-content-editor'),
                'default' => 'show_all',
                'list' => array(
                    'show_all' => __("Show All", 'motopress-content-editor'),
                    'load_more' => __("Load More Button", 'motopress-content-editor'),
                    'pagination' => __("Pagination", 'motopress-content-editor')
                )
            ),
            'load_more_text' => array(
                'type' => 'text',
                'label' => __("Load More button text", 'motopress-content-editor'),
                'default' => __("Load More", 'motopress-content-editor'), // "Load More"
                'dependency' => array(
                    'parameter' => 'display_style',
                    'value' => 'load_more'
                )
            ),
            'filter' => array(
                'type' => 'radio-buttons',
                'label' => __("Filter", 'motopress-content-editor'),
                'description' => __("Add taxonomy filter.", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'cats' => __("First Taxonomy", 'motopress-content-editor'),
                    'tags' => __("Second Taxonomy", 'motopress-content-editor'),
                    'both' => __("Both", 'motopress-content-editor')
                ),
				'dependency' => array(
					'parameter' => 'query_type',
					'value' => 'simple'
				)
			),
			'filter_tax_1' => array(
				'type' => 'select',
				'label' => __("Select First Taxonomy", 'motopress-content-editor'),
				'description' => '',
				'default' => 'category',
				'list' => MPCEShortcode::getTaxonomiesList('category', false),
				'dependency' => array(
					'parameter' => 'filter',
					'value' => array( 'cats', 'both' )
				)
			),
			'filter_tax_2' => array(
				'type' => 'select',
				'label' => __("Select Second Taxonomy", 'motopress-content-editor'),
				'description' => '',
				'default' => 'post_tag',
				'list' => MPCEShortcode::getTaxonomiesList('post_tag', false),
				'dependency' => array(
					'parameter' => 'filter',
					'value' => array( 'tags', 'both' )
				)
			),
            'filter_btn_color' => array(
                'type' => 'color-select',
                'label' => __("Button color", 'motopress-content-editor'),
                'default' => 'motopress-btn-color-silver',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'motopress-btn-color-silver' => __("Silver", 'motopress-content-editor'),
                    'motopress-btn-color-red' => __("Red", 'motopress-content-editor'),
                    'motopress-btn-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'motopress-btn-color-warm' => __("Warm", 'motopress-content-editor'),
                    'motopress-btn-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'motopress-btn-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'motopress-btn-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'motopress-btn-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'motopress-btn-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'motopress-btn-color-violet' => __("Violet", 'motopress-content-editor'),
                    'motopress-btn-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'motopress-btn-color-black' => __("Black", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'filter',
                    'except' => 'none'
                )
            ),
			'filter_btn_divider' => array(
				'type' => 'text',
				'label' => __("Divider", 'motopress-content-editor'),
				'default' => '/',
				'dependency' => array(
					'parameter' => 'filter_btn_color',
					'value' => 'none'
				)
			),
            'filter_cats_text' => array(
                'type' => 'text',
                'label' => __("First Filter Title", 'motopress-content-editor'),
                'default' => __('Categories') . ':',
                'dependency' => array(
                    'parameter' => 'filter',
                    'value' => array('cats', 'both')
                )
            ),
            'filter_tags_text' => array(
                'type' => 'text',
                'label' => __("Second Filter Title", 'motopress-content-editor'),
                'default' => __('Tags') . ':',
                'dependency' => array(
                    'parameter' => 'filter',
                    'value' => array('tags', 'both')
                )
            ),
            'filter_all_text' => array(
                'type' => 'text',
                'label' => __("\"View All\" text", 'motopress-content-editor'),
                'default' => __('All'),
                'dependency' => array(
                    'parameter' => 'filter',
                    'except' => 'none'
                )
            ),
        ), 10);
        $postsGridObj->addStyle(array(
            'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-posts-grid-basic',
                    'label' => __("Posts Grid", 'motopress-content-editor')
                )
            )
        ));

/* MODAL */
        $modalObj = new MPCEObject(MPCEShortcode::PREFIX . 'modal', __("Modal", 'motopress-content-editor'), "modal.png", array(
            'content' => array(
				'type' => 'longtext-tinymce',
                'label' => __("Content", 'motopress-content-editor'),
				'text' => __("Edit", 'motopress-content-editor') . ' ' . __("Modal", 'motopress-content-editor'),
                'default' => __("<h1>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h1><p>Integer ac leo ut arcu dictum viverra at eu magna. Integer ut eros varius, ornare magna non, malesuada nunc. Nulla elementum fringilla libero vitae luctus. Phasellus tincidunt nulla erat, in consectetur ante ornare tempor. Curabitur egestas purus ac gravida malesuada. Vestibulum sit amet rhoncus nisi. Quisque porta enim eget nisi luctus accumsan. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>", 'motopress-content-editor'),
				'saveInContent' => 'true'
            ),
			'modal_style' => array(
				'type' => 'radio-buttons',
				'label' => __("Style", 'motopress-content-editor'),
				'default' => 'dark',
				'list' => array(
					'dark' => __("Dark", 'motopress-content-editor'),
					'light' => __("Light", 'motopress-content-editor'),
					'custom' => __("Custom", 'motopress-content-editor')
				)
			),
			'modal_shadow_color' => array(
				'type' => 'color-picker',
                'label' => __("Background color", 'motopress-content-editor'),
                'default' => '#0b0b0b',
				'dependency' => array(
					'parameter' => 'modal_style',
					'value' => 'custom'
				)
			),
			'modal_content_color' => array(
				'type' => 'color-picker',
                'label' => __("Box color", 'motopress-content-editor'),
                'default' => '#ffffff',
				'dependency' => array(
					'parameter' => 'modal_style',
					'value' => 'custom'
				)
			),
			'button_text' => array(
                'type' => 'text',
                'label' => __("Text on the button", 'motopress-content-editor'),
                'default' => 'Open Modal Box'
            ),
            'button_full_width' => array(
                'type' => 'checkbox',
                'label' => __("Stretch", 'motopress-content-editor') . ' ' . __("Button", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'button_align' => array(
                'type' => 'radio-buttons',
                'label' => __("Button", 'motopress-content-editor') . ' ' . __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_full_width',
                    'value' => 'false'
                )
            ),
            'button_icon' => array(
                'type' => 'icon-picker',
                'label' => __("Button", 'motopress-content-editor') . ' ' . __("Icon", 'motopress-content-editor'),
                'default' => 'none',
                'list' => $this->getIconClassList(true)
            ),
            'button_icon_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon", 'motopress-content-editor') . ' ' . __("Icon alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_icon',
                    'except' => 'none'
                )
            ),
			'show_animation' => array(
				'type' => 'select',
				'label' => __("Show animation", 'motopress-content-editor'),
				'default' => '',
				'list' => array(
					'' => 'None',
					'bounce' => 'Bounce',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
					'bounceIn' => 'Bounce In',
					'bounceInDown' => 'Bounce In Down',
					'bounceInLeft' => 'Bounce In Left',
					'bounceInRight' => 'Bounce In Right',
					'bounceInUp' => 'Bounce In Up',
					'fadeIn' => 'Fade In',
					'fadeInDown' => 'Fade In Down',
					'fadeInDownBig' => 'Fade In Down Big',
					'fadeInLeft' => 'Fade In Left',
					'fadeInLeftBig' => 'Fade In Left Big',
					'fadeInRight' => 'Fade In Right',
					'fadeInRightBig' => 'Fade In Right Big',
					'fadeInUp' => 'Fade In Up',
					'fadeInUpBig' => 'Fade In Up Big',
					'flip' => 'Flip',
					'flipInX' => 'Flip In X',
					'flipInY' => 'Flip In Y',
					'lightSpeedIn' => 'Light Speed In',
					'rotateIn' => 'Rotate In',
					'rotateInDownLeft' => 'Rotate In Down Left',
					'rotateInDownRight' => 'Rotate In Down Right',
					'rotateInUpLeft' => 'Rotate In Up Left',
					'rotateInUpRight' => 'Rotate In Up Right',
					'rollIn' => 'Roll In',
					'zoomIn' => 'Zoom In',
					'zoomInDown' => 'Zoom In Down',
					'zoomInLeft' => 'Zoom In Left',
					'zoomInRight' => 'Zoom In Right',
					'zoomInUp' => 'Zoom In Up',
					'slideInDown' => 'Slide In Down',
					'slideInLeft' => 'Slide In Left',
					'slideInRight' => 'Slide In Right',
					'slideInUp' => 'Slide In Up',
				)
			),
			'hide_animation' => array(
				'type' => 'select',
				'label' => __("Hide animation", 'motopress-content-editor'),
				'default' => '',
				'list' => array(
					'' => 'None',
					'auto' => 'Auto',
					'bounce' => 'Bounce',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
					'bounceOut' => 'Bounce Out',
					'bounceOutDown' => 'Bounce Out Down',
					'bounceOutLeft' => 'Bounce Out Left',
					'bounceOutRight' => 'Bounce Out Right',
					'bounceOutUp' => 'Bounce Out Up',
					'fadeOut' => 'Fade Out',
					'fadeOutDown' => 'Fade Out Down',
					'fadeOutDownBig' => 'Fade Out Down Big',
					'fadeOutLeft' => 'Fade Out Left',
					'fadeOutLeftBig' => 'Fade Out Left Big',
					'fadeOutRight' => 'Fade Out Right',
					'fadeOutRightBig' => 'Fade Out Right Big',
					'fadeOutUp' => 'Fade Out Up',
					'fadeOutUpBig' => 'Fade Out Up Big',
					'flip' => 'Flip',
					'flipOutX' => 'Flip Out X',
					'flipOutY' => 'Flip Out Y',
					'lightSpeedOut' => 'Light Speed Out',
					'rotateOut' => 'Rotate Out',
					'rotateOutDownLeft' => 'Rotate Out Down Left',
					'rotateOutDownRight' => 'Rotate Out Down Right',
					'rotateOutUpLeft' => 'Rotate Out Up Left',
					'rotateOutUpRight' => 'Rotate Out Up Right',
					'rollOut' => 'Roll Out',
					'zoomOut' => 'Zoom Out',
					'zoomOutDown' => 'Zoom Out Down',
					'zoomOutLeft' => 'Zoom Out Left',
					'zoomOutRight' => 'Zoom Out Right',
					'zoomOutUp' => 'Zoom Out Up',
					'slideOutDown' => 'Slide Out Down',
					'slideOutLeft' => 'Slide Out Left',
					'slideOutRight' => 'Slide Out Right',
					'slideOutUp' => 'Slide Out Up',
				)
			)
        ), 50, MPCEObject::ENCLOSED);
		$modalStyles = $buttonStyles;
		$modalStyles['mp_style_classes']['selector'] = '> button';
		$modalStyles['mp_custom_style']['selector'] = '> button';
		$modalObj->addStyle($modalStyles);

/* SPLASH SCREEN */
		$popupObj = new MPCEObject(MPCEShortcode::PREFIX . 'popup', __("Splash Screen", 'motopress-content-editor'), "popup.png", array(
            'content' => array(
				'type' => 'longtext-tinymce',
                'label' => __("Content", 'motopress-content-editor'),
				'text' => __("Edit", 'motopress-content-editor') . ' ' . __("Splash Screen", 'motopress-content-editor'),
                'default' => __("<h1>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</h1><p>Integer ac leo ut arcu dictum viverra at eu magna. Integer ut eros varius, ornare magna non, malesuada nunc. Nulla elementum fringilla libero vitae luctus. Phasellus tincidunt nulla erat, in consectetur ante ornare tempor. Curabitur egestas purus ac gravida malesuada. Vestibulum sit amet rhoncus nisi. Quisque porta enim eget nisi luctus accumsan. Interdum et malesuada fames ac ante ipsum primis in faucibus.</p>", 'motopress-content-editor'),
				'saveInContent' => 'true'
            ),
			'delay' => array(
				'type' => 'text',
				'label' => __("Delay in milliseconds", 'motopress-content-editor'),
				'default' => '1000'
			),
			'display' => array(
				'type' => 'select',
				'label' => __("Display", 'motopress-content-editor'),
				'list' => array(
					'' => __("Always", 'motopress-content-editor'),
					'once' => __("Once", 'motopress-content-editor')
				),
				'default' => 'false'				
			),
			'modal_style' => array(
				'type' => 'radio-buttons',
				'label' => __("Style", 'motopress-content-editor'),
				'default' => 'dark',
				'list' => array(
					'dark' => __("Dark", 'motopress-content-editor'),
					'light' => __("Light", 'motopress-content-editor'),
					'custom' => __("Custom", 'motopress-content-editor')
				)
			),
			'modal_shadow_color' => array(
				'type' => 'color-picker',
                'label' => __("Background color", 'motopress-content-editor'),
                'default' => '#0b0b0b',
				'dependency' => array(
					'parameter' => 'modal_style',
					'value' => 'custom'
				)
			),
			'modal_content_color' => array(
				'type' => 'color-picker',
                'label' => __("Box color", 'motopress-content-editor'),
                'default' => '#ffffff',
				'dependency' => array(
					'parameter' => 'modal_style',
					'value' => 'custom'
				)
			),
			'show_animation' => array(
				'type' => 'select',
				'label' => __("Show animation", 'motopress-content-editor'),
				'default' => 'slideInDown',
				'list' => array(
					'' => 'None',
					'bounce' => 'Bounce',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
					'bounceIn' => 'Bounce In',
					'bounceInDown' => 'Bounce In Down',
					'bounceInLeft' => 'Bounce In Left',
					'bounceInRight' => 'Bounce In Right',
					'bounceInUp' => 'Bounce In Up',
					'fadeIn' => 'Fade In',
					'fadeInDown' => 'Fade In Down',
					'fadeInDownBig' => 'Fade In Down Big',
					'fadeInLeft' => 'Fade In Left',
					'fadeInLeftBig' => 'Fade In Left Big',
					'fadeInRight' => 'Fade In Right',
					'fadeInRightBig' => 'Fade In Right Big',
					'fadeInUp' => 'Fade In Up',
					'fadeInUpBig' => 'Fade In Up Big',
					'flip' => 'Flip',
					'flipInX' => 'Flip In X',
					'flipInY' => 'Flip In Y',
					'lightSpeedIn' => 'Light Speed In',
					'rotateIn' => 'Rotate In',
					'rotateInDownLeft' => 'Rotate In Down Left',
					'rotateInDownRight' => 'Rotate In Down Right',
					'rotateInUpLeft' => 'Rotate In Up Left',
					'rotateInUpRight' => 'Rotate In Up Right',
					'rollIn' => 'Roll In',
					'zoomIn' => 'Zoom In',
					'zoomInDown' => 'Zoom In Down',
					'zoomInLeft' => 'Zoom In Left',
					'zoomInRight' => 'Zoom In Right',
					'zoomInUp' => 'Zoom In Up',
					'slideInDown' => 'Slide In Down',
					'slideInLeft' => 'Slide In Left',
					'slideInRight' => 'Slide In Right',
					'slideInUp' => 'Slide In Up',
				)
			),
			'hide_animation' => array(
				'type' => 'select',
				'label' => __("Hide animation", 'motopress-content-editor'),
				'default' => 'slideOutUp',
				'list' => array(
					'' => 'None',
					'auto' => 'Auto',
					'bounce' => 'Bounce',
					'pulse' => 'Pulse',
					'rubberBand' => 'Rubber Band',
					'shake' => 'Shake',
					'swing' => 'Swing',
					'tada' => 'Tada',
					'wobble' => 'Wobble',
					'jello' => 'Jello',
					'bounceOut' => 'Bounce Out',
					'bounceOutDown' => 'Bounce Out Down',
					'bounceOutLeft' => 'Bounce Out Left',
					'bounceOutRight' => 'Bounce Out Right',
					'bounceOutUp' => 'Bounce Out Up',
					'fadeOut' => 'Fade Out',
					'fadeOutDown' => 'Fade Out Down',
					'fadeOutDownBig' => 'Fade Out Down Big',
					'fadeOutLeft' => 'Fade Out Left',
					'fadeOutLeftBig' => 'Fade Out Left Big',
					'fadeOutRight' => 'Fade Out Right',
					'fadeOutRightBig' => 'Fade Out Right Big',
					'fadeOutUp' => 'Fade Out Up',
					'fadeOutUpBig' => 'Fade Out Up Big',
					'flip' => 'Flip',
					'flipOutX' => 'Flip Out X',
					'flipOutY' => 'Flip Out Y',
					'lightSpeedOut' => 'Light Speed Out',
					'rotateOut' => 'Rotate Out',
					'rotateOutDownLeft' => 'Rotate Out Down Left',
					'rotateOutDownRight' => 'Rotate Out Down Right',
					'rotateOutUpLeft' => 'Rotate Out Up Left',
					'rotateOutUpRight' => 'Rotate Out Up Right',
					'rollOut' => 'Roll Out',
					'zoomOut' => 'Zoom Out',
					'zoomOutDown' => 'Zoom Out Down',
					'zoomOutLeft' => 'Zoom Out Left',
					'zoomOutRight' => 'Zoom Out Right',
					'zoomOutUp' => 'Zoom Out Up',
					'slideOutDown' => 'Slide Out Down',
					'slideOutLeft' => 'Slide Out Left',
					'slideOutRight' => 'Slide Out Right',
					'slideOutUp' => 'Slide Out Up',
				)
			)
        ), 55, MPCEObject::ENCLOSED);
		$popupObj->addStyle(array(
			'mp_style_classes' => array(
				'basic' => array(
					'class' => 'motopress-popup-basic',
					'label' => __("Splash Screen", 'motopress-content-editor')
				),
				'selector' => false
			),
			'mp_custom_style' => array(
				'selector' => false
			)
		));

/* SERVICE BOX */
		$serviceBoxObj = new MPCEObject(MPCEShortcode::PREFIX . 'service_box', __("Service Box", 'motopress-content-editor'), 'service-box.png', array(
            'layout' => array(
                'type' => 'select',
                'label' => __("Content style", 'motopress-content-editor'),
                'default' => 'centered',
                'list' => array(
                    'centered' => __("Centered", 'motopress-content-editor'),
                    'heading-float' => __("Title align right", 'motopress-content-editor'),
                    'text-heading-float' => __("Title & text align right", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'except' => 'big_image'
                )
            ),
            'icon_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Media type", 'motopress-content-editor'),
                'default' => 'font',
                'list' => array(
                    'font' => __("Font Icon", 'motopress-content-editor'),
                    'image' => __("Image Icon", 'motopress-content-editor'),
                    'big_image' => __("Wide Image", 'motopress-content-editor')
                )
            ),
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'fa fa-star-o',
                'list' => $this->getIconClassList(),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => 'font'
                ),
            ),
            'icon_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon size", 'motopress-content-editor'),
                'default' => 'normal',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor'),
                    'normal' => __("Normal", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'extra-large' => __("Extra Large", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => 'font'
                )
            ),
            'icon_custom_size' => array(
                'type' => 'spinner',
                'label' => __("Icon custom size", 'motopress-content-editor'),
                'description' => __("Font size in px", 'motopress-content-editor'),
                'min' => 1,
                'step' => 1,
                'max' => 500,
                'default' => 26,
                'dependency' => array(
                    'parameter' => 'icon_size',
                    'value' => 'custom'
                )
            ),
            'icon_color' => array(
                'type' => 'color-select',
                'label' => __("Icon color", 'motopress-content-editor'),
                'default' => 'mp-text-color-default',
                'list' => array(
                    'mp-text-color-default' => __("Silver", 'motopress-content-editor'),
                    'mp-text-color-red' => __("Red", 'motopress-content-editor'),
                    'mp-text-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'mp-text-color-warm' => __("Warm", 'motopress-content-editor'),
                    'mp-text-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'mp-text-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'mp-text-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'mp-text-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'mp-text-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'mp-text-color-violet' => __("Violet", 'motopress-content-editor'),
                    'mp-text-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'mp-text-color-black' => __("Black", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => 'font'
                )
            ),
            'icon_custom_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon custom color", 'motopress-content-editor'),
                'default' => '#000000',
                'dependency' => array(
                    'parameter' => 'icon_color',
                    'value' => 'custom'
                )
            ),
            'image_id' => array(
                'type' => 'image',
                'label' => __("Icon image", 'motopress-content-editor'),
                'default' => '',
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => array('image', 'big_image')
                )
            ),
            'image_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon image size", 'motopress-content-editor'),
                'default' => 'thumbnail',
                'list' => array(
                    'thumbnail' => __("Thumbnail", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                    'full' => __("Full", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => 'image'
                )
            ),
            'image_custom_size' => array(
                'type' => 'text',
                'label' => __("Icon image size", 'motopress-content-editor'),
                'description' => __("Image size in pixels, ex. 50x50 or theme-registered image size. Note: the closest-sized image will be used if original one does not exist.", 'motopress-content-editor'),
                'default' => '50x50',
                'dependency' => array(
                    'parameter' => 'image_size',
                    'value' => 'custom'
                )
            ),
            'big_image_height' => array(
                'type' => 'spinner',
                'label' => __("Image height", 'motopress-content-editor'),
                'default' => 150,
                'min' => 1,
                'max' => 1000,
                'step' => 1,
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'value' => 'big_image'
                )
            ),
            'icon_background_type' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon background", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'square' => __("Square", 'motopress-content-editor'),
                    'rounded' => __("Rounded", 'motopress-content-editor'),
                    'circle' => __("Circle", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'icon_type',
                    'except' => 'big_image'
                )
            ),
            'icon_background_size' => array(
                'type' => 'spinner',
                'label' => __("Icon background size", 'motopress-content-editor'),
                'default' => 1.5,
                'min' => 1,
                'max' => 3,
                'step' => 0.1,
                'dependency' => array(
                    'parameter' => 'icon_background_type',
                    'except' => 'none'
                )
            ),
            'icon_background_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon background color", 'motopress-content-editor'),
                'default' => '#000000',
                'dependency' => array(
                    'parameter' => 'icon_background_type',
                    'except' => 'none'
                )
            ),
            'icon_margin_left' => array(
                'type' => 'spinner',
                'label' => __("Icon margin Left", 'motopress-content-editor'),
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => '0'
            ),
            'icon_margin_right' => array(
                'type' => 'spinner',
                'label' => __("Icon margin Right", 'motopress-content-editor'),
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => '0'
            ),
            'icon_margin_top' => array(
                'type' => 'spinner',
                'label' => __("Icon margin Top", 'motopress-content-editor'),
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => '0'
            ),
            'icon_margin_bottom' => array(
                'type' => 'spinner',
                'label' => __("Icon margin Bottom", 'motopress-content-editor'),
                'min' => 0,
                'max' => 500,
                'step' => 1,
                'default' => '0'
            ),
            'icon_effect' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon effect", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'grayscale' => __("Grayscale", 'motopress-content-editor'),
                    'zoom' => __("Zoom", 'motopress-content-editor'),
                    'rotate' => __("Rotate", 'motopress-content-editor')
                )
            ),
            'heading' => array(
                'type' => 'longtext',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',
                'text' => __("Open in WordPress Editor", 'motopress-content-editor'),
                'saveInContent' => 'false'
            ),
            'heading_tag' => array(
                'type' => 'radio-buttons',
                'label' => __("Title style", 'motopress-content-editor'),
                'default' => 'h2',
                'list' => array(
                    'h1' => 'H1',
                    'h2' => 'H2',
                    'h3' => 'H3',
                    'h4' => 'H4',
                    'h5' => 'H5',
                    'h6' => 'H6'
                )
            ),
            'text' => array(
                'type' => 'longtext-tinymce',
                'label' => __("Content", 'motopress-content-editor'),
                'default' => '<p>Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</p>',
                'text' => __("Open in WordPress Editor", 'motopress-content-editor'),
                'saveInContent' => 'true'
            ),
            'button_show' => array(
                'type' => 'checkbox',
                'label' => __("Show button", 'motopress-content-editor'),
                'default' => 'true'
            ),
            'button_text' => array(
                'type' => 'text',
                'label' => __("Button text", 'motopress-content-editor'),
                'default' => 'Button',
                'dependency' => array(
                    'parameter' => 'button_show',
                    'value' => 'true'
                )
            ),
            'button_link' => array(
                'type' => 'link',
                'label' => __("Button link", 'motopress-content-editor'),
                'default' => '#',
                'dependency' => array(
                    'parameter' => 'button_show',
                    'value' => 'true'
                )
            ),
            'button_color' => array(
                'type' => 'color-select',
                'label' => __("Button color", 'motopress-content-editor'),
                'default' => 'motopress-btn-color-silver',
                'list' => array(
                    'motopress-btn-color-silver' => __("Silver", 'motopress-content-editor'),
                    'motopress-btn-color-red' => __("Red", 'motopress-content-editor'),
                    'motopress-btn-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'motopress-btn-color-warm' => __("Warm", 'motopress-content-editor'),
                    'motopress-btn-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'motopress-btn-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'motopress-btn-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'motopress-btn-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'motopress-btn-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'motopress-btn-color-violet' => __("Violet", 'motopress-content-editor'),
                    'motopress-btn-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'motopress-btn-color-black' => __("Black", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'button_show',
                    'value' => 'true'
                )
            ),
            'button_custom_bg_color' => array(
                'type' => 'color-picker',
                'label' => __("Button background color", 'motopress-content-editor'),
                'default' => '#ffffff',
                'dependency' => array(
                    'parameter' => 'button_color',
                    'value' => 'custom'
                )
            ),
            'button_custom_text_color' => array(
                'type' => 'color-picker',
                'label' => __("Button text color", 'motopress-content-editor'),
                'default' => '#000000',
                'dependency' => array(
                    'parameter' => 'button_color',
                    'value' => 'custom'
                )
            ),
            'button_align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'center',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_show',
                    'value' => 'true'
                )
            )
        ), 15, MPCEObject::ENCLOSED);

        $serviceBoxObj->addStyle(array(
           'mp_style_classes' => array(
               'basic' => array(
                   'class' => 'motopress-service-box-basic',
                   'label' => __("Service Box", 'motopress-content-editor')
               )
           )
        ));

/* LIST */
        $listObj = new MPCEObject(MPCEShortcode::PREFIX . 'list', __("List", 'motopress-content-editor'), 'list.png', array(
            'list_type' => array(
                'type' => 'select',
                'label' => __("Style", 'motopress-content-editor'),
                'default' => 'icon',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'icon' => __("Icon", 'motopress-content-editor'),
                    'circle' => __("Circle", 'motopress-content-editor'),
                    'disc' => __("Disc", 'motopress-content-editor'),
                    'square' => __("Square", 'motopress-content-editor'),
                    'armenian' => __("Armenian", 'motopress-content-editor'),
                    'georgian' => __("Georgian", 'motopress-content-editor'),
                    'decimal' => '1, 2, 3, 4',
                    'decimal-leading-zero' => '01, 02, 03, 04',
                    'lower-latin' => 'a, b, c, d',
                    'lower-roman' => 'i, ii, iii, iv',
                    'lower-greek' => 'α, β, γ, δ',
                    'upper-latin' => 'A, B, C, D',
                    'upper-roman' => 'I, II, III, IV'
                )
            ),			
            'items' => array(
                'type' => 'longtext-table',
                'label' => __("List elements", 'motopress-content-editor'),
                'default' => 'Lorem<br />Ipsum<br />Dolor',
                'saveInContent' => 'true'
            ),
			'use_custom_text_color' => array(
				'type' => 'checkbox',
				'label' => __("Custom text color", 'motopress-content-editor'),
				'default' => 'false'
			), 
            'text_color' => array(
                'type' => 'color-picker',
                'label' => __("Text color", 'motopress-content-editor'),
                'default' => '#000000',
				'dependency' => array(
                    'parameter' => 'use_custom_text_color',
                    'except' => 'false'
                )
            ),
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'fa fa-star',
                'list' => $this->getIconClassList(),
                'dependency' => array(
                    'parameter' => 'list_type',
                    'value' => 'icon'
                )
            ),
			'use_custom_icon_color' => array(
				'type' => 'checkbox',
				'label' => __("Custom icon color", 'motopress-content-editor'),
				'default' => 'false',
                'dependency' => array(
                    'parameter' => 'list_type',
                    'value' => 'icon'
                )
			),
			'icon_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon color", 'motopress-content-editor'),
                'default' => '#000000',
				'dependency' => array(
                    'parameter' => 'use_custom_icon_color',
                    'except' => 'false'
                )
            ),
        ), 60, MPCEObject::ENCLOSED);
		$listObj->addStyle(array(
			'mp_style_classes' => array(
				'basic' => array(
					'class' => 'motopress-list-obj-basic',
					'label' => __("List", 'motopress-content-editor')
				)
           )
		));

        $buttonInnerObj = new MPCEObject(MPCEShortcode::PREFIX . 'button_inner', __("Button", 'motopress-content-editor'), null, array(
			'text' => array(
                'type' => 'text',
                'label' => __("Text on the button", 'motopress-content-editor'),
                'default' => __("Button", 'motopress-content-editor')
            ),
            'link' => array(
                'type' => 'link',
                'label' => __("Link", 'motopress-content-editor'),
                'default' => '#',
                'description' => __("ex. http://yoursite.com/ or /blog", 'motopress-content-editor')
            ),
            'target' => array(
                'type' => 'checkbox',
                'label' => __("Open link in new window (tab)", 'motopress-content-editor'),
                'default' => 'false'
            ),
            'color' => array(
                'type' => 'color-select',
                'label' => __("Button color", 'motopress-content-editor'),
                'default' => 'motopress-btn-color-silver',
                'list' => array(
                    'custom' => __("Custom", 'motopress-content-editor'),
                    'motopress-btn-color-silver' => __("Silver", 'motopress-content-editor'),
                    'motopress-btn-color-red' => __("Red", 'motopress-content-editor'),
                    'motopress-btn-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'motopress-btn-color-warm' => __("Warm", 'motopress-content-editor'),
                    'motopress-btn-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'motopress-btn-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'motopress-btn-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'motopress-btn-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'motopress-btn-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'motopress-btn-color-violet' => __("Violet", 'motopress-content-editor'),
                    'motopress-btn-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'motopress-btn-color-black' => __("Black", 'motopress-content-editor')
                )
            ),
            'custom_color' => array(
                'type' => 'color-picker',
                'label' => __("Custom button color", 'motopress-content-editor'),
                'default' => '#000000',
                'dependency' => array(
                    'parameter' => 'color',
                    'value' => 'custom'
                )
            ),
            'icon' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'none',
                'list' => $this->getIconClassList(true),
            )
		), null, MPCEObject::SELF_CLOSED, MPCEObject::RESIZE_NONE, false);
		
		$buttonInnerObj->addStyle(array(
			'mp_style_classes' => array(
				'basic' => array(
					'class' => 'motopress-btn',
					'label' => __("Button", 'motopress-content-editor')
				)
			)
		));		
				
/* BUTTON GROUP */        
        $buttonGroupObj = new MPCEObject(MPCEShortcode::PREFIX . 'button_group', __("Button Group", 'motopress-content-editor'), 'button-group.png', array(
            'elements' => array(
                'type' => 'group',
                'contains' => MPCEShortcode::PREFIX . 'button_inner',
                'items' => array(
                    'label' => array(
                        'default' => __("Text on the button", 'motopress-content-editor'),
                        'parameter' => 'text'
                    ),
                    'count' => 2
                ),
                'text' => sprintf(__("Add new %s item", 'motopress-content-editor'), __("Button", 'motopress-content-editor')),
                /*'activeParameter' => 'active',
                'rules' => array(
                    'rootSelector' => '.motopress-button-obj > .motopress-btn',
                    'activeSelector' => '',
                    'activeClass' => 'ui-state-active'
                ),
                'events' => array(
                    'onActive' => array(
                        'selector' => '> a',
                        'event' => 'click'
                    )
                )*/
            ),
            'align' => array(
                'type' => 'radio-buttons',
                'label' => __("Alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            ),
			'group_layout' => array(
                'type' => 'radio-buttons',
                'label' => __("Layout", 'motopress-content-editor'),
                'default' => 'horizontal',
                'list' => array(
                    'horizontal' => __("Horizontal", 'motopress-content-editor'),
                    'vertical' => __("Vertical", 'motopress-content-editor')
                )
            ),
            'indent' => array(
                'type' => 'radio-buttons',
                'label' => __("Indent", 'motopress-content-editor'),
                'default' => '5',
				'list' => array(
                    '0' => '0',
                    '2' => '2',
                    '5' => '5',
                    '10' => '10',
                    '15' => '15',
                    '25' => '25',
                )
            ),
            'size' => array(
                'type' => 'radio-buttons',
                'label' => __("Buttons size", 'motopress-content-editor'),
                'default' => 'middle',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor'),
                    'middle' => __("Middle", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor')
                )
            ),
            'icon_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            ),
            'icon_indent' => array(
                'type' => 'select',
                'label' => __("Icon indent", 'motopress-content-editor'),
                'default' => 'small',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor'),
                    'middle' => __("Middle", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor') . ' ' . __("Icon indent", 'motopress-content-editor')
                )
            )
        ), 20, MPCEObject::ENCLOSED);

/* CALL TO ACTION */
        $ctaObj = new MPCEObject(MPCEShortcode::PREFIX . 'cta', __("Call To Action", 'motopress-content-editor'), 'call-to-action.png', array(
            'heading' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => 'Lorem ipsum dolor'
            ),
            'subheading' => array(
                'type' => 'text',
                'label' => __("Subtitle", 'motopress-content-editor'),
                'default' => 'Lorem ipsum dolor sit amet'
            ),
			'content_text' => array(
                'type' => 'longtext',
                'label' => __("Text", 'motopress-content-editor'),
                'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ),
            'text_align' => array(
                'type' => 'radio-buttons',
                'label' => __("Text alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor'),
                    'justify' => __("Justify", 'motopress-content-editor')
                )
            ),
            'shape' => array(
                'type' => 'radio-buttons',
                'label' => __("Shape", 'motopress-content-editor'),
                'default' => 'rounded',
                'list' => array(
                    'square' => __("Square", 'motopress-content-editor'),
                    'rounded' => __("Rounded", 'motopress-content-editor'),
                    'round' => __("Round", 'motopress-content-editor')
                )
            ),
            'style' => array(
                'type' => 'select',
                'label' => __("Style", 'motopress-content-editor'),
                'default' => '3d',
                'list' => array(
                    'classic' => __("Classic", 'motopress-content-editor'),
                    'flat' => __("Flat", 'motopress-content-editor'),
                    'outline' => __("Outline", 'motopress-content-editor'),
                    '3d' => __("3D", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                )
            ),
            'style_bg_color' => array(
                'type' => 'color-picker',
                'label' => __("Background Color", 'motopress-content-editor'),
                'default' => '#ffffff',
                'dependency' => array(
                    'parameter' => 'style',
                    'value' => 'custom'
                )
            ),
            'style_text_color' => array(
                'type' => 'color-picker',
                'label' => __("Text Color", 'motopress-content-editor'),
                'default' => '#000000',
                'dependency' => array(
                    'parameter' => 'style',
                    'value' => 'custom'
                )
            ),
            'width' => array(
                'type' => 'spinner',
                'label' => __("Width (%)", 'motopress-content-editor'),
                'min' => 50,
                'max' => 100,
                'step' => 1,
                'default' => 100
            ),
            'button_pos' => array(
                'type' => 'select',
                'label' => __("Button position", 'motopress-content-editor'),
                'default' => 'right',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top' => __("Top", 'motopress-content-editor'),
                    'bottom' => __("Bottom", 'motopress-content-editor'),
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            ),
			'button_text' => array(
                'type' => 'text',
                'label' => __("Text on the button", 'motopress-content-editor'),
                'default' => __("Button", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_link' => array(
                'type' => 'link',
                'label' => __("Button link", 'motopress-content-editor'),
                'default' => '#',
                'description' => __("ex. http://yoursite.com/ or /blog", 'motopress-content-editor'),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_target' => array(
                'type' => 'checkbox',
                'label' => __("Open link in new window (tab)", 'motopress-content-editor'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_align' => array(
                'type' => 'radio-buttons',
                'label' => __("Button alignment", 'motopress-content-editor'),
                'default' => 'center',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'center' => __("Center", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
				'dependency' => array(
					'parameter' => 'button_pos',
                    'value' => array('top', 'bottom')
				)
            ),
            'button_shape' => array(
                'type' => 'radio-buttons',
                'label' => __("Button shape", 'motopress-content-editor'),
                'default' => 'rounded',
                'list' => array(
                    'square' => __("Square", 'motopress-content-editor'),
                    'rounded' => __("Rounded", 'motopress-content-editor'),
                    'round' => __("Round", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_color' => array(
                'type' => 'color-select',
                'label' => __("Button color", 'motopress-content-editor'),
                'default' => 'motopress-btn-color-silver',
                'list' => array(
                    'motopress-btn-color-silver' => __("Silver", 'motopress-content-editor'),
                    'motopress-btn-color-red' => __("Red", 'motopress-content-editor'),
                    'motopress-btn-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'motopress-btn-color-warm' => __("Warm", 'motopress-content-editor'),
                    'motopress-btn-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'motopress-btn-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'motopress-btn-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'motopress-btn-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'motopress-btn-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'motopress-btn-color-violet' => __("Violet", 'motopress-content-editor'),
                    'motopress-btn-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'motopress-btn-color-black' => __("Black", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Buttons size", 'motopress-content-editor'),
                'default' => 'large',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor'),
                    'middle' => __("Middle", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_icon' => array(
                'type' => 'icon-picker',
                'label' => __("Button icon", 'motopress-content-editor'),
                'default' => 'none',
                'list' => $this->getIconClassList(true),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'button_icon_position' => array(
                'type' => 'radio-buttons',
                'label' => __("Button icon alignment", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_icon',
                    'except' => 'none'
                )
            ),
            'button_animation' => array(
                'type' => 'select',
                'label' => __("Button animation", 'motopress-content-editor'),
                'default' => 'right-to-left',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top-to-bottom' => __("Top to bottom", 'motopress-content-editor'),
                    'bottom-to-top' => __("Bottom to top", 'motopress-content-editor'),
                    'left-to-right' => __("Left to right", 'motopress-content-editor'),
                    'right-to-left' => __("Right to left", 'motopress-content-editor'),
                    'appear' => __("Appear", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'button_pos',
                    'except' => 'none'
                )
            ),
            'icon_pos' => array(
                'type' => 'select',
                'label' => __("Icon position", 'motopress-content-editor'),
                'default' => 'left',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top' => __("Top", 'motopress-content-editor'),
                    'bottom' => __("Bottom", 'motopress-content-editor'),
                    'left' => __("Left", 'motopress-content-editor'),
                    'right' => __("Right", 'motopress-content-editor')
                )
            ),
			'icon_type' => array(
                'type' => 'icon-picker',
                'label' => __("Icon", 'motopress-content-editor'),
                'default' => 'fa fa-info-circle',
                'list' => $this->getIconClassList(),
                'dependency' => array(
                    'parameter' => 'icon_pos',
                    'except' => 'none'
                )
            ),
            'icon_color' => array(
                'type' => 'color-select',
                'label' => __("Icon color", 'motopress-content-editor'),
                'default' => 'custom',
                'list' => array(
                    'mp-text-color-default' => __("Silver", 'motopress-content-editor'),
                    'mp-text-color-red' => __("Red", 'motopress-content-editor'),
                    'mp-text-color-pink-dreams' => __("Pink Dreams", 'motopress-content-editor'),
                    'mp-text-color-warm' => __("Warm", 'motopress-content-editor'),
                    'mp-text-color-hot-summer' => __("Hot Summer", 'motopress-content-editor'),
                    'mp-text-color-olive-garden' => __("Olive Garden", 'motopress-content-editor'),
                    'mp-text-color-green-grass' => __("Green Grass", 'motopress-content-editor'),
                    'mp-text-color-skyline' => __("Skyline", 'motopress-content-editor'),
                    'mp-text-color-aqua-blue' => __("Aqua Blue", 'motopress-content-editor'),
                    'mp-text-color-violet' => __("Violet", 'motopress-content-editor'),
                    'mp-text-color-dark-grey' => __("Dark Grey", 'motopress-content-editor'),
                    'mp-text-color-black' => __("Black", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'icon_pos',
                    'except' => 'none'
                )
            ),
            'icon_custom_color' => array(
                'type' => 'color-picker',
                'label' => __("Icon custom color", 'motopress-content-editor'),
                'default' => '#ffffff',
                'dependency' => array(
                    'parameter' => 'icon_color',
                    'value' => 'custom'
                )
            ),
            'icon_size' => array(
                'type' => 'radio-buttons',
                'label' => __("Icon size", 'motopress-content-editor'),
                'default' => 'extra-large',
                'list' => array(
                    'mini' => __("Mini", 'motopress-content-editor'),
                    'small' => __("Small", 'motopress-content-editor'),
                    'normal' => __("Normal", 'motopress-content-editor'),
                    'large' => __("Large", 'motopress-content-editor'),
                    'extra-large' => __("Extra Large", 'motopress-content-editor'),
                    'custom' => __("Custom", 'motopress-content-editor'),
                ),
                'dependency' => array(
                    'parameter' => 'icon_pos',
                    'except' => 'none'
                )
            ),
            'icon_custom_size' => array(
                'type' => 'spinner',
                'label' => __("Icon custom size", 'motopress-content-editor'),
                'description' => __("Font size in px", 'motopress-content-editor'),
                'min' => 1,
                'step' => 1,
                'max' => 500,
                'default' => 26,
                'dependency' => array(
                    'parameter' => 'icon_size',
                    'value' => 'custom'
                )
            ),
            'icon_on_border' => array(
                'type' => 'checkbox',
                'label' => __("Place icon on border", 'motopress-content-editor'),
                'default' => 'false',
                'dependency' => array(
                    'parameter' => 'icon_pos',
                    'except' => 'none'
                )
            ),
            'icon_animation' => array(
                'type' => 'select',
                'label' => __("Icon animation", 'motopress-content-editor'),
                'default' => 'left-to-right',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top-to-bottom' => __("Top to bottom", 'motopress-content-editor'),
                    'bottom-to-top' => __("Bottom to top", 'motopress-content-editor'),
                    'left-to-right' => __("Left to right", 'motopress-content-editor'),
                    'right-to-left' => __("Right to left", 'motopress-content-editor'),
                    'appear' => __("Appear", 'motopress-content-editor')
                ),
                'dependency' => array(
                    'parameter' => 'icon_pos',
                    'except' => 'none'
                )
            ),
            'animation' => array(
                'type' => 'select',
                'label' => __("Effect of appearance", 'motopress-content-editor'),
                'default' => 'none',
                'list' => array(
                    'none' => __("None", 'motopress-content-editor'),
                    'top-to-bottom' => __("Top to bottom", 'motopress-content-editor'),
                    'bottom-to-top' => __("Bottom to top", 'motopress-content-editor'),
                    'left-to-right' => __("Left to right", 'motopress-content-editor'),
                    'right-to-left' => __("Right to left", 'motopress-content-editor'),
                    'appear' => __("Appear", 'motopress-content-editor')
                )
            )            
        ), 45);
		$ctaObj->addStyle(array(
			'mp_style_classes' => array(
                'basic' => array(
                    'class' => 'motopress-cta-obj-basic',
                    'label' => 'Call To Action'
                ),
            )
		));

/* SLIDER PLUGIN */
        $mpSliderObj = null;
        if (is_plugin_active('motopress-slider/motopress-slider.php') || is_plugin_active('motopress-slider-lite/motopress-slider.php')) {
            global $mpsl_settings;
            if (version_compare($mpsl_settings['plugin_version'], '1.1.2', '>=')) {
	            /** @var MPSlider $mpSlider */
                global $mpSlider;
                $mpSliderObj = new MPCEObject('mpsl', apply_filters('mpsl_product_name', __("MotoPress Slider", 'motopress-content-editor')), 'layer-slider.png', array(
                    'alias' => array(
                        'type' => 'select',
                        'label' => __("Select slider", 'motopress-content-editor'),
                        'description' => __("Select slider from the list of sliders you created for this website.", 'motopress-content-editor'),
                        'list' => array_merge(
                            array('' => __("- select -", 'motopress-content-editor')),
                            $mpSlider->getSliderList('title', 'alias')
                        )
                    )
                ), 40);
            }
        }

// WORDPRESS
        // WP Widgets Area
        global $wp_registered_sidebars;
        $wpWidgetsArea_array = array();
        $wpWidgetsArea_default = '';
        if ( $wp_registered_sidebars ){
            foreach ( $wp_registered_sidebars as $sidebar ) {
                if (empty($wpWidgetsArea_default))
                        $wpWidgetsArea_default = $sidebar['id'];
                $wpWidgetsArea_array[$sidebar['id']] = $sidebar['name'];
            }
        }else {
            $wpWidgetsArea_array['no'] = __("There are no sidebars", 'motopress-content-editor');
        }
        $wpWidgetsAreaObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_widgets_area', __("Widgets Area", 'motopress-content-editor'), 'sidebar.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => '',
                'description' => __("Use this widget to add one of your Widget Areas.", 'motopress-content-editor')
            ),
            'sidebar' => array(
                'type' => 'select',
                'label' => __("Select Area", 'motopress-content-editor'),
                'default' => $wpWidgetsArea_default,
                'description' => '',
                'list' => $wpWidgetsArea_array
            )
        ), 5);

        // archives
        $wpArchiveObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_archives', __("Archives", 'motopress-content-editor'), 'archives.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Archives", 'motopress-content-editor'),
                'description' => __("A monthly archive of your site posts", 'motopress-content-editor')
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => __("Display as dropdown", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => __("Show post counts", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            )
        ), 45);

        // calendar
        $wpCalendarObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_calendar', __("Calendar", 'motopress-content-editor'), 'calendar.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Calendar", 'motopress-content-editor'),
                'description' => __("A calendar of your site posts", 'motopress-content-editor')
            )
        ), 30);

        // wp_categories
        $wpCategoriesObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_categories', __("Categories", 'motopress-content-editor'), 'categories.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Categories", 'motopress-content-editor'),
                'description' => __("A list or dropdown of categories", 'motopress-content-editor')
            ),
            'dropdown' => array(
                'type' => 'checkbox',
                'label' => __("Display as dropdown", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            ),
            'count' => array(
                'type' => 'checkbox',
                'label' => __("Show post counts", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            ),
            'hierarchy' => array(
                'type' => 'checkbox',
                'label' => __("Show hierarchy", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            )
        ), 40);

        // wp_navmenu
        $wpCustomMenu_menus = get_terms('nav_menu');
        $wpCustomMenu_array = array();
        $wpCustomMenu_default = '';
        if ($wpCustomMenu_menus){
            foreach($wpCustomMenu_menus as $menu){
                if (empty($wpCustomMenu_default))
                    $wpCustomMenu_default = $menu->slug;
                $wpCustomMenu_array[$menu->slug] = $menu->name;
            }
        }else{
            $wpCustomMenu_array['no'] = __("There are no menus", 'motopress-content-editor');
        }
        $wpCustomMenuObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_navmenu', __("Custom Menu", 'motopress-content-editor'), 'custom-menu.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Custom Menu", 'motopress-content-editor'),
                'description' => __("Use this widget to add one of your custom menus as a widget.", 'motopress-content-editor')
            ),
            'nav_menu' => array(
                'type' => 'select',
                'label' => __("Select Menu", 'motopress-content-editor'),
                'default' => $wpCustomMenu_default,
                'description' => '',
                'list' => $wpCustomMenu_array
            )
        ), 10);

        // wp_meta
        $wpMetaObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_meta', __("Meta", 'motopress-content-editor'), 'meta.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Meta", 'motopress-content-editor'),
                'description' => __("Log in/out, admin, feed and WordPress links", 'motopress-content-editor')
            )
        ), 55);

        // wp_pages
        $wpPagesObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_pages', __("Pages", 'motopress-content-editor'), 'pages.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Pages", 'motopress-content-editor'),
                'description' => __("Your site WordPress Pages", 'motopress-content-editor')
            ),
            'sortby' => array(
                'type' => 'select',
                'label' => __("Sort by", 'motopress-content-editor'),
                'default' => 'menu_order',
                'description' => '',
                'list' => array(
                    'post_title' => __("Page title", 'motopress-content-editor'),
                    'menu_order' => __("Page order", 'motopress-content-editor'),
                    'ID' => __("Page ID", 'motopress-content-editor')
                ),
            ),
            'exclude' => array(
                'type' => 'text',
                'label' => __("Exclude", 'motopress-content-editor'),
                'default' => '',
                'description' => __("Page IDs, separated by commas.", 'motopress-content-editor')
            )
        ), 15);

        // wp_posts
        $wpPostsObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_posts', __("Recent Posts", 'motopress-content-editor'), 'recent-posts.png', array(
            'title' => array(
                    'type' => 'text',
                    'label' => __("Title", 'motopress-content-editor'),
                    'default' => __("Recent Posts", 'motopress-content-editor'),
                    'description' => __("The most recent posts on your site", 'motopress-content-editor')
            ),
            'number' => array(
                    'type' => 'text',
                    'label' => __("Number of Posts to show", 'motopress-content-editor'),
                    'default' => '5',
                    'description' => ''
            ),
            'show_date' => array(
                    'type' => 'checkbox',
                    'label' => __("Display post date?", 'motopress-content-editor'),
                    'default' => '',
                    'description' => ''
            )
        ), 20);

        // wp_comments
        $wpRecentCommentsObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_comments', __("Recent Comments", 'motopress-content-editor'), 'recent-comments.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Recent Comments", 'motopress-content-editor'),
                'description' => __("The most recent comments", 'motopress-content-editor')
            ),
            'number' => array(
                'type' => 'text',
                'label' => __("Number of Comments to show", 'motopress-content-editor'),
                'default' => '5',
                'description' => ''
            )
        ), 25);

        // wp_rss
        $wpRSSObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_rss', __("RSS", 'motopress-content-editor'), 'rss.png', array(
            'url' => array(
                'type' => 'text',
                'label' => __("RSS feed URL", 'motopress-content-editor'),
                'default' => 'https://motopress.com/feed/',
                'description' => __("Enter the RSS feed URL here", 'motopress-content-editor')
            ),
            'title' => array(
                'type' => 'text',
                'label' => __("Feed title", 'motopress-content-editor'),
                'default' => '',
                'description' => __("Give the feed a title (optional)", 'motopress-content-editor')
            ),
            'items' => array(
                'type' => 'select',
                'label' => __("Items quantity", 'motopress-content-editor'),
                'default' => 9,
                'description' => __("How many items would you like to display?", 'motopress-content-editor'),
                'list' => range(1, 20),
            ),
            'show_summary' => array(
                'type' => 'checkbox',
                'label' => __("Display item content?", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            ),
            'show_author' => array(
                'type' => 'checkbox',
                'label' => __("Display item author if available?", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            ),
            'show_date' => array(
                'type' => 'checkbox',
                'label' => __("Display item date?", 'motopress-content-editor'),
                'default' => '',
                'description' => ''
            )
        ), 50);

        // search
        $wpSearchObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_search', __("Search", 'motopress-content-editor'), 'search.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Search", 'motopress-content-editor'),
                'description' => __("A search form for your site", 'motopress-content-editor')
            )
        ), 35);

        // tag cloud
        $wpTagCloudObj = new MPCEObject(MPCEShortcode::PREFIX . 'wp_tagcloud', __("Tag Cloud", 'motopress-content-editor'), 'tag-cloud.png', array(
            'title' => array(
                'type' => 'text',
                'label' => __("Title", 'motopress-content-editor'),
                'default' => __("Tags", 'motopress-content-editor'),
                'description' => __("Your most used tags in cloud format", 'motopress-content-editor')
            ),
            'taxonomy' => array(
                'type' => 'select',
                'label' => __("Taxonomy", 'motopress-content-editor'),
                'default' => 10,
                'description' => '',
                'list' => array(
                    'post_tag' => __("Tags", 'motopress-content-editor'),
                    'category' => __("Categories", 'motopress-content-editor'),
                )
            )
        ), 60);
        /* wp widgets END */

        /* Groups */
        $gridGroup = new MPCEGroup();
        $gridGroup->setId(MPCEShortcode::PREFIX . 'grid');
        $gridGroup->setName(__("Grid", 'motopress-content-editor'));
        $gridGroup->setShow(false);
        $gridGroup->addObject(array($rowObj, $rowInnerObj, $spanObj, $spanInnerObj));

        $textGroup = new MPCEGroup();
        $textGroup->setId(MPCEShortcode::PREFIX . 'text');
        $textGroup->setName(__("Text", 'motopress-content-editor'));
        $textGroup->setIcon('text.png');
        $textGroup->setPosition(0);
        $textGroup->addObject(array($textObj /*20*/, $headingObj /*10*/, $codeObj /*30*/, $quotesObj /*40*/, $membersObj /*50*/, $listObj /*60*/, $iconObj /*70*/));

        $imageGroup = new MPCEGroup();
        $imageGroup->setId(MPCEShortcode::PREFIX . 'image');
        $imageGroup->setName(__("Image", 'motopress-content-editor'));
        $imageGroup->setIcon('image.png');
        $imageGroup->setPosition(10);
        $imageGroup->addObject(array($imageObj, $imageSlider, $gridGalleryObj, $mpSliderObj));

        $buttonGroup = new MPCEGroup();
        $buttonGroup->setId(MPCEShortcode::PREFIX . 'button');
        $buttonGroup->setName(__("Button", 'motopress-content-editor'));
        $buttonGroup->setIcon('button.png');
        $buttonGroup->setPosition(20);
        $buttonGroup->addObject(array($buttonObj, $downloadButtonObj, $buttonInnerObj, $buttonGroupObj, $socialsObj, $socialProfileObj));

        $mediaGroup = new MPCEGroup();
        $mediaGroup->setId(MPCEShortcode::PREFIX . 'media');
        $mediaGroup->setName(__("Media", 'motopress-content-editor'));
        $mediaGroup->setIcon('media.png');
        $mediaGroup->setPosition(30);
        $mediaGroup->addObject(array($videoObj, $wpAudioObj));

        $otherGroup = new MPCEGroup();
        $otherGroup->setId(MPCEShortcode::PREFIX . 'other');
        $otherGroup->setName(__("Other", 'motopress-content-editor'));
        $otherGroup->setIcon('other.png');
        $otherGroup->setPosition(40);
        $otherGroup->addObject(array(
            $postsGridObj,          /* 10 */
            $serviceBoxObj,         /* 15 */
            $tabsObj,               /* 20 */
            $accordionObj,          /* 25 */
            $tableObj,              /* 30 */
            $postsSliderObj,        /* 35 */
            $ctaObj,                /* 45 */
            $modalObj,              /* 50 */
            $popupObj,              /* 55 */
            $spaceObj,              /* 60 */
            $gMapObj,               /* 65 */
            $countdownTimerObj,     /* 70 */
            $embedObj,              /* 75 */
            $googleChartsObj,       /* 80 */
            $tabObj,
            $accordionItemObj,
        ));

        $wordpressGroup = new MPCEGroup();
        $wordpressGroup->setId(MPCEShortcode::PREFIX . 'wordpress');
        $wordpressGroup->setName(__("WordPress", 'motopress-content-editor'));
        $wordpressGroup->setIcon('wordpress.png');
        $wordpressGroup->setPosition(50);
        $wordpressGroup->addObject(array($wpArchiveObj, $wpCalendarObj, $wpCategoriesObj, $wpCustomMenuObj, $wpMetaObj, $wpPagesObj, $wpPostsObj, $wpRecentCommentsObj, $wpRSSObj, $wpSearchObj, $wpTagCloudObj, $wpWidgetsAreaObj));

        self::$defaultGroup = $otherGroup->getId();

        $this->addGroup(array($gridGroup, $textGroup, $imageGroup, $buttonGroup, $mediaGroup, $otherGroup, $wordpressGroup));

        $this->updateDeprecatedParams();

        do_action_ref_array('mp_library', array(&$this));
    }

    /**
     * @return MPCEGroup[]
     */
    public function getLibrary() {
        return $this->library;
    }

    /**
     * @param string $id
     * @return MPCEGroup|boolean
     */
    public function &getGroup($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->library)) {
                        return $this->library[$id];
                    }
                }
            }
        }
        $group = false;
        return $group;
    }

    /**
     * @param MPCEGroup|MPCEGroup[] $group
     */
    public function addGroup($group) {
        if ($group instanceof MPCEGroup) {
            if ($group->isValid()) {
                if (!array_key_exists($group->getId(), $this->library)) {
                    if (count($group->getObjects()) > 0) {
                        $this->library[$group->getId()] = $group;
                    }
                }
            } else {
                if (!self::$isAjaxRequest) {
                    $group->showErrors();
                }
            }
        } elseif (is_array($group)) {
            if (!empty($group)) {
                foreach ($group as $g) {
                    if ($g instanceof MPCEGroup) {
                        if ($g->isValid()) {
                            if (!array_key_exists($g->getId(), $this->library)) {
                                if (count($g->getObjects()) > 0) {
                                    $this->library[$g->getId()] = $g;
                                }
                            }
                        } else {
                            if (!self::$isAjaxRequest) {
                                $g->showErrors();
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $id
     * @return boolean
     */
    public function removeGroup($id) {
        if (is_string($id)) {
            $id = trim($id);
            if (!empty($id)) {
                $id = filter_var($id, FILTER_SANITIZE_STRING);
                if (preg_match(MPCEBaseElement::ID_REGEXP, $id)) {
                    if (array_key_exists($id, $this->library)) {
                        unset($this->library[$id]);
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param string $id
     * @return MPCEObject|boolean
     */
    public function &getObject($id) {
        foreach ($this->library as $group) {
            $object = &$group->getObject($id);
            if ($object) return $object;
        }
        $object = false;
        return $object;
    }

    /**
     * @param MPCEObject|MPCEObject[] $object
     * @param string $group [optional]
     */
    public function addObject($object, $group = 'mp_other') {
        $groupObj = &$this->getGroup($group);
        if (!$groupObj) { //for support versions less than 1.5 where group id without MPCEShortcode::PREFIX
            $groupObj = &$this->getGroup(MPCEShortcode::PREFIX . $group);
        }
        if (!$groupObj) {
            $groupObj = &$this->getGroup(self::$defaultGroup);
        }
        if ($groupObj) {
            $groupObj->addObject($object);
        }
    }

    /**
     * @param string $id
     */
    public function removeObject($id) {
        foreach ($this->library as $group) {
            if ($group->removeObject($id)) break;
        }
    }

    /**
     * @return array
     * @deprecated 3.0.0
     */
    public function getTemplates() {
        return array();
    }

    /**
     * @param string $id
     * @return boolean
     *
     * @deprecated
     */
    public function &getTemplate($id) {
    	return false;
    }

    /**
     * @param $template
     * @deprecated 3.0.0
     */
    public function addTemplate($template) {}

    /**
     * @param string $id
     * @return boolean
     * @deprecated 3.0.0
     */
    public function removeTemplate($id) {
        return false;
    }

    /**
     * @return array
     */
    public function getData() {
        $library = array(
            'groups' => array(),
            'globalPredefinedClasses' => array(),
            'tinyMCEStyleFormats' => array(),
            'templates' => array(),
            'grid' => array()
        );
        foreach ($this->library as $group) {
            if (count($group->getObjects()) > 0) {
                uasort($group->objects, array(__CLASS__, 'positionCmp'));
                $library['groups'][$group->getId()] = $group;
            }
        }
        uasort($library['groups'], array(__CLASS__, 'positionCmp'));
        $library['globalPredefinedClasses'] = $this->globalPredefinedClasses;
        $library['tinyMCEStyleFormats'] = $this->tinyMCEStyleFormats;
        $library['templates'] = $this->templates;
        $library['grid'] = $this->gridObjects;
        return $library;
    }

    /**
     * @return array
     */
    public function getObjectsList() {
        $list = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object) {
                $parameters = $object->getParameters();
                if (!empty($parameters)) {
                    foreach ($parameters as $key => $value) {
                        unset($parameters[$key]);
                        $parameters[$key] = array();
                    }
                }

                $list[$object->getId()] = array(
                    'parameters' => $parameters,
                    'group' => $group->getId()
                );
            }
        }
        return $list;
    }

    /**
     * @return array
     */
    public function getObjectsNames() {
        $names = array();
        foreach ($this->library as $group){
            foreach ($group->getObjects() as $object){
                $names[] = $object->getId();
            }
        }
        return $names;
    }

    /**
     * @static
     * @param MPCEObject $a
     * @param MPCEObject $b
     * @return int
     */
    /*
    public static function nameCmp(MPCEObject $a, MPCEObject $b) {
        return strcmp($a->getName(), $b->getName());
    }
    */

    /**
     * @param MPCEElement $a
     * @param MPCEElement $b
     * @return int
     */
    public function positionCmp(MPCEElement $a, MPCEElement $b) {
        $aPosition = $a->getPosition();
        $bPosition = $b->getPosition();
        if ($aPosition == $bPosition) {
            return 0;
        }
        return ($aPosition < $bPosition) ? -1 : 1;
    }

    /**
     * @return boolean
     */
    private function isAjaxRequest() {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ? true : false;
    }

    private function extendPredefinedWithGoogleFonts(&$predefined){
        $fontClasses = get_option('motopress_google_font_classes', array());
        if (!empty($fontClasses)) {
            $items = array();
            foreach ($fontClasses as $fontClassName => $fontClass) {
                $items[$fontClass['fullname']] = array(
                    'class' => $fontClass['fullname'],
                    'label' => $fontClassName,
                    'external' => mpceSettings()['google_font_classes_dir_url'] . $fontClass['file']
                );
                if (!empty($fontClass['variants'])){
                    foreach($fontClass['variants'] as $variant){
                        $items[$fontClass['fullname'] . '-' . $variant] = array(
                            'class' => $fontClass['fullname'] . '-' . $variant,
                            'label' => $fontClassName . ' ' . $variant,
                            'external' => mpceSettings()['google_font_classes_dir_url'] . $fontClass['file']
                        );
                    }
                }
            }
            $googleFontClasses = array(
                'label' => __("Google Fonts", 'motopress-content-editor'),
                'values' => $items
            );
            $predefined['google-font-classes'] = $googleFontClasses;
        }
    }

    public function getGridObjects(){
        return $this->gridObjects;
    }

    public function getGroupObjects(){
        $groupObjects = array();
        foreach($this->library as $group) {
            if (isset($group->objects)) {
                foreach ($group->objects as $objectName=>$object){
                    if (isset($object->parameters)) {
                        foreach($object->parameters as $parameter){
                            if ($parameter['type'] === 'group') {
                                $groupObjects[] = $objectName;
                            }
                        }
                    }
                }
            }
        }
        return $groupObjects;
    }

    public function setGrid($grid){

        if (is_array($grid)
            && isset($grid['row'])
            && isset($grid['span'])
        ){
            if (!isset($grid['row']['edgeclass'])) {
                $grid['row']['edgeclass'] = $grid['row']['class'];
            }
            // Backward compatibility
            if (!isset($grid['span']['custom_class_attr'])) {
                $grid['span']['custom_class_attr'] = 'mp_style_classes';
            }
            $grid['span']['minclass'] = $grid['span']['class'] . 1;
            $grid['span']['fullclass'] = $grid['span']['class'] . $grid['row']['col'];

            $this->gridObjects = $grid;
        }
    }
    public function setRow($rowArgs){
        $this->gridObjects['row'] = $rowArgs;
    }

    public function setSpan($spanArgs){
        $this->gridObjects['span'] =$spanArgs;
    }

    private function updateDeprecatedParams() {
        foreach ($this->library as $grp) {
            foreach ($grp->objects as $objName => $obj) {
                if (isset($obj->styles) && array_key_exists('mp_style_classes', $obj->styles)) {
                    if (!array_key_exists($objName, $this->deprecatedParameters)) {
                        $this->deprecatedParameters[$objName] = array();
                    }
                    if (!array_key_exists('custom_class', $this->deprecatedParameters[$objName])) {
                        $this->deprecatedParameters[$objName]['custom_class'] = array('prefix' => '');
                    }
                }
            }
        }
    }

	/**
	 * @param bool $useEmptyIcon
	 * @return array
	 */
    public function getIconClassList($useEmptyIcon = false) {

    	$IconList = include(mpceSettings()['plugin_dir_path'] . 'includes/ce/icon_list.php');

        if ($useEmptyIcon) {
	        $empty = array(
		        'none' => array(
			        'class' => 'fa',
			        'label' => 'None'
		        )
	        );
	        $IconList = array_merge($empty, $IconList);
        }

        return $IconList;
    }
}