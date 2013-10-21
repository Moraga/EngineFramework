{
	portal: 'My Website',
	station: 'Some Section',
	channel: 'Some Category',
	title: 'My Posts',
	media: 'post',
	keywords: '',
	
	export: {
		main: {
			url: '',
			template: 'post.html'
		},
	},
	
	modules: {
		post <Post>: {
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
				
				text: {
					type: 'html',
					title: 'Text',
				},
				
				tags: {
					type: 'tags',
					title: 'Tags (dev)',
				}
			},
			
			image <Featured image>: {
				image: {
					type: 'file',
					title: 'Image',
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
		},
		
		format <Format>: {
			settings <Settings>: {
				format: {
					type: 'radio',
					options: ['Standard', 'Aside', 'Image', 'Video', 'Quote', 'Link'],
					default: 'Standard',
				}
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