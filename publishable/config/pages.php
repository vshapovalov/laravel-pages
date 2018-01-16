<?php
/*
	Menu declaration:

		Menu section contains menu items:

		[
			// unique name of menu item
			'name' => 'media-library',

			// caption of menu item
			'caption' => 'Медиа библиотека',

			// bus event name, 'medialibrary:mount' is default for media library,
			// 'crud:%crudcode%:mount' is default for cruds
			'action' => 'medialibrary:mount'

			// this component will be mount after admin panel loaded
			'default' => true,

			// items can be grouped
			// array of nested items, contains same item
			// nested items can contains items too
			'items' => [

			]
		],


    Crud declaration

		Crud options:
					'name'    => 'Пользователи', // name of crud, displays in admin navbar
					'code'    => 'users', // crud code and url for api requests
					'model'   => 'App\User', // crud using model
					'id'      => 'id', // model's primarykey
					'display' => 'name', // crud field for crud table and controls
					'visible' => true, // is crud visible in admin menu
					'type'    => 'list', // crud type ['list', 'tree'], if you want to use tree type, then use Treeable trait for model
					'scopes'  => [  // model scopes for getting crud rows
								'byUser'    => [
									'name'   => 'orderByName', // model scope function name, example, orderByName for scopeOrderByName
									'params' => [ // params for crud model scopes when crud in pick mode on edit panel, resolved from root item, injected in scope
										'id'
									]
								],
								'onlyWithImages' => [ // example without params
									'name' => 'onlyWithImages'
								]
							],

		Crud field options:

					'name'       => 'password' // model's field or relation name
					'json'       => true, // indicates that field uses value of json field, in this case name must be like 'meta->bio->gender'
					'caption'    => 'Пароль' // field caption for crud table and control
					'type'       => 'textbox' // crud field type, control type
					'visibility' => [ 'browse', 'edit', 'add' ] // visibility and state of control in crud table and edit panel
					'tab'        => 'Основные параметры' // tab name for control in edit panel
					'validation' => 'required|string:255' // laravel validation rule, except relation field type
					'readonly'   => true // is field readonly on editpanel in [edit, add] states
					'additional' => [] // additional control options
					'relation' => [] // options for fields of relation type
					'dynamic'    => [] // options for fields of dynamic type

		Crud field types:

				textbox - simple textbox
					has additional options: additional: ['slugify' => 'depending field name']
					has additional options: additional: ['mode' => 'password']
					has additional options: additional: ['mode' => 'masked', 'mask' => '+7(777)000-00-00']

				colorbox - color picker based on html5 input[type="color"]

				checkbox - checkbox based on input[type="checkbox"]

				textarea - simple textarea

				datepicker - date picker, has additional options
					additional: ['mode' => ['date', 'datetime']]

				richedit - richeditor by tinymce

				dropdown - simple dropdown, has additional options

					additional : [
						'mode' => 'single', // single, multi
						'values' => [
							[ 'key' => 0, 'value' => 'DRAFT'],
							[ 'key' => 1, 'value' => 'PUBLISHED']
						]
					]

				image - image picker, based on crud media library, has additional options
					additional: [
						'mode' => 'single' or 'multi' // 'multi' is default
						'resize' => [ // resize uploaded image
							'width' => 1000,
							'height' => null,
							'quality' => 90
						],
						'thumbnails' => [ // create thumbnails
							[
								'name' => 'medium',
								'scale' => 50 // scale mode
							],
							[
								'name' => 'small',
								'scale' => 25
							],
							[
								'name' => 'cropped',
								'crop' => [ // crop mode
									'width' => 250,
									'height' => 250,
								]
							],
							[
								'name' => 'fitted',
								'fit' => [ // fit mode
									'width' => 250,
									'height' => 250,
								]
							]
						]
					]

				dynamic - field type depends on other crud model field, field has options

					'additional'    => [
						'type' => 'related', // relation type
						'from' => 'crudFieldType.code' // {relation field name}.{relation field attribute}
									or
						'type' => 'field', // field type
						'from' => 'fieldtype' // field name
					]

				relation - relation field type, field has options

					'relation'   => [
							'name'  => 'colors', // relation name
							'crud'  => 'colors', // crud used for field control, must be declared in crud config
							'type'  => 'belongsToMany', // relation type, one of ['belongsTo', 'belongsToMany', 'hasOne', 'hasMany']
							'pivot' => [ // pivot fields options if using pivot table, relation should have method ->withPivot([...])
								'fields' => [
									'qty'         => [
										'name'       => 'qty',      // pivot field name
										'caption'    => 'Изображения', // pivot field caption for crud table
										'type'       => 'image',    // control type for pivot field,
																	// can be one of types:
																	//   textbox, checkbox, textarea, image, richedit ... not relations, may be later

										'additional' => [           // additional field options, example for image field type
											'mode' => 'multi'
										]
									],
									'publishDate' => [
										'name'       => 'publishDate',
										'caption'    => 'Дата',
										'type'       => 'datepicker',
										'additional' => [
											'mode' => 'date'
										]
									],
								]
							]
						]
					'additional' => [
								'buttons' => ['add', 'pick', 'delete_all']
							]
	 */

return
	[
		/* route prefixes */
		'crud_prefix' => 'cruds',
		'media_prefix' => 'media',

		/* default media library settings, this can be overriden in field options */
		'media_default_settings' => [
		/* for example
			'resize' => [
				'width' => 1000,
				'height' => null,
				'quality' => 90
			],
			'thumbnails' => [
				[
					'name' => 'medium',
					'scale' => 50
				],
				[
					'name' => 'small',
					'scale' => 25
				],
				[
					'name' => 'cropped',
					'crop' => [
						'width' => 250,
						'height' => 250,
					]
				],
				[
					'name' => 'fitted',
					'fit' => [
						'width' => 250,
						'height' => 250,
						'position' => 'center' //top-left, top, top-right, left, center (default), right, bottom-left, bottom, bottom-right
					]
				],
			]
		*/
		],


		/* additional script and user components scripts declaration */

		'components' => [
//			[
//				'name' => 'test-component', // just simple name
//				'path' => '/js/test-components.js' // path to component, must be absolute
//
//              user component must be registered by action, which specified in user component script
//              example of user component can be found in %package%/resource/assets/js/example-user-component/
//				action field value = user:testcomponent:mount
//			]
		],
	];