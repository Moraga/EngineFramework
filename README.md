Engine Framework with Publisher and Auth plugins
===============

Installation
--------------

1. Create the database and user
2. Open the install.php in your browser

Publisher
--------------

Publisher is a flexible CMS for building dynamic sites.

The Publisher works with medias and meta-templates. With this combination you can create many structures: news, blog posts, recipes, albums, modules, ...

Media
--------------

The medias are the basic for meta-templates definition.

To create a media, go to the media's directory, create a new file and set up the JSON file with the required information.

	{
		title: 'Title of the media', // Required
		uri: '{Y}/{m}/{d}/{title}.html', // Values between {} are meta-template fields or date characteres, required
		rank: number, // The media's position in the admin's homepage, optional
		
		database: {
			columns: {
				columnName: {
					type: 'MySQL field types', // Required
					value: 'Meta-template fields', // Required
					length: number, // Optional
					default: 'Default value', // Optional
				},
				
				...
			},
			
			// Optional
			index: {
				indexName: ['column names above', ...],
				
				...
			}
		}
	}

After create your media an update is necessary in the admin's settings page:

1. Go to yoursite.com/admin/settings
2. Click in Media > Update
3. Select the media
4. Submit

After that the database structure will be created. At this moment you can update a media just one time. To update again you will need delete the database table manually and re-run the media creation steps above.

Meta-template
--------------

The meta-templates defines the admin forms and how the data will be accessible in template.

To create a meta-template, go to the directory of the meta-templates and create a new file. After that, your meta-template will be available and listed on the homepage of the admin, inside the meta-template's media.

	{
		portal: 'Your site', // Required 
		station: 'Category', // Required
		channel: 'Sub category', // Required
		title: 'Title of the meta-template', // Required
		media: 'Name of the media file without .js', // Required
		keywords: 'tags, ...', // in development // Required
		
		export: {
			main: {
				url: 'base/url/', // Required
				template: 'file.html' // Required
			}
		},
		
		modules: {
			post <Title for this group>: {
				content <Title for this group>: {
					fieldName: {
						type: 'text|radio|checkbox|select|textarea|html|file|email|url|number|tags', // Required
						title: 'Title of the field', // Optional
						description: 'Description of the field', // Optional
						default: 'Default value', // Optional
						options: ['A', 'B', ...], // For checkbox and radio
						unsigned: true|false, // For numbers
						required: true|false, // Optional
						regex: '', // Validation, optional
						multiple: true|false, // Optional
						charcount: true, // Optional
						minlength: number, // Optional
						maxlength: number, // Optional
						
						// for images (in development)
						minwidth: number, // Optional
						maxwidth: number, // Optional
						minheight: number, // Optional
						maxheight: number, // Optional
						ratio: number, // Optional
					}
				},
				
				...
			},
			
			...
		}
	}

Content URL
--------------

The URL of the meta-template and the URI of the media are merged creating the URL of the content. So, for the examples above we will have: /base/url/2013/10/22/title-of-the-post.html

Template
--------------

Opens the contents set by media and meta-template. The base directory is: webcontent/

Each template file set in meta-template must exists in this directory.

**Accessing data**

	<?= $groupName->groupName->fieldName ?>

Example:

	<?= $post->content->title ?>

For multiples and checkboxes the values are in arrays.

**Doing queries**

	<?php

	search(array(
		'media'		=> '',
		'portal'	=> '',
		'station'	=> '',
		'channel'	=> '',
		'station'	=> '',
		'q'			=> '', // query string
		'p'			=> '', // page number
	));

	// or

	search('media=&portal=...');

	?>

**Fetching data**

	<?php

	while (have_results()) {
		echo $result->title;
	}

	?>

The values available are the column names defined in the media.

**Pagination**

	<?= pagination() ?>

Creating other files, like index.html

Just create in your webcontent's directory. It will be available in the root of your site.

**Javascript and CSS**

You can also create those in webcontent's directory. The CSS will be automatically minified.

**Adding Javascript and CSS**

You can use the arrays $js and $css to put your files. The advantage of this usage is that the CSS wil be automasclyy placed inside the head tag and the Javascript will be placed before of the end of the body tag.

Example:

	<?php

	// my template file

	$css[] = 'style.css';
	$js[]  = 'script.js';

	?>

Users and groups
--------------

Add, edit and remove at:
yoursite.com/admin/settings

Considerations
--------------

This is the initial phase of the project. Updates and new features will come soon.
