{
	title: 'News',
	uri: '{Y}/{m}/{d}/{title}.html',
	rank: 10,
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