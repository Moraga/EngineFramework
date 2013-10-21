{
	title: 'Post',
	uri: '{Y}/{m}/{d}/{title}.html',
	database: {
		columns: {
			title: {
				type: 'varchar',
				length: 255,
				value: 'title',
			},
			
			text: {
				type: 'text',
				value: 'text',
			}
		},
		
		index: {
			title: ['title'],
		}
	}
}