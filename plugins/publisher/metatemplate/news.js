{
	portal: 'My Website',
	station: 'Some Section',
	channel: 'Some Category',
	title: 'News',
	media: 'news',
	keywords: '',
	
	export: {
		main: {
			url: 'news/',
			template: 'news.html'
		},
	},
	
	modules: {
		news <News>: {
			content <Content>: {
				title: {
					type: 'text',
					title: 'Title',
					description: 'Enter title here',
					required: true,
					charcount: true,
				},
				
				author: {
					type: 'text',
					title: 'Author',
					description: 'Name of the author',
				},
				
				translator: {
					type: 'text',
					title: 'Translator',
					description: 'Name of the translator',
				},
				
				update: {
					type: 'checkbox',
					title: 'Update',
					options: [[1, 'Show date of last update']]
				},
				
				text: {
					type: 'html',
					title: 'Text',
				},
				
				tags: {
					type: 'tags',
					title: 'Tags (dev)',
				}
			},
			
			tags <Tags>: {
				local: {
					type: 'tags',
					title: 'Local(s)',
				},
				
				event: {
					type: 'tags',
					title: 'Events',
				},
				
				other: {
					type: 'tags',
					title: 'Other tags',
				},
			},
		},
		
		image <Images>: {
			page <Page>: {
				first: {
					type: 'file',
					title: 'First image (highlight)',
					description: 'URL of the image',
				},
				
				credit: {
					type: 'text',
					title: 'Credit',
					description: 'Photographer/Source/License',
				},
				
				legend: {
					type: 'text',
					title: 'Legend/title',
				},
			},
			
			newsletter <Newsletter>: {
				first: {
					type: 'file',
					title: 'Newsletter',
					description: 'URL of the image',
				},
				
				credit: {
					type: 'text',
					title: 'Credit',
					description: 'Photographer/Source/License',
				},
				
				legend: {
					type: 'text',
					title: 'Legend/title',
				},
			}
		},
		
		comments <Comments (dev)>: {
			settings <Settings>: {
				allow: {
					type: 'radio',
					title: 'Allow comments?',
					options: [[1, 'Yes'], [0, 'No']],
					default: 1,
				}
			}
		},
		
		interaction <Interaction>: {
			facebook <Facebook>: {
				title: {
					type: 'text',
					title: 'Title',
				},
				
				description: {
					type: 'textarea',
					title: 'Description',
				},
				
				image: {
					type: 'file',
					title: 'Image',
					description: 'URL of the image (300x300)',
				}
			},
			
			seo <SEO>: {
				description: {
					type: 'text',
					title: 'Description',
				},
				
				keywords: {
					type: 'text',
					title: 'Keywords',
				},
				
				robots: {
					type: 'select',
					title: 'Robots',
					options: ['index, follow', 'noindex, follow', 'index, nofollow', 'noindex, nofollow']
				}
			}
		}
	}
}