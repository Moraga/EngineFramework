{
	title: 'Recipes',
	uri: '{title}.html',
	database: {
		columns: {
			title: {
				type: 'varchar',
				length: 255,
				value: 'title',
			},
			
			description: {
				type: 'varchar',
				length: 500,
				value: 'description',
			},
			
			difficult: {
				type: 'varchar',
				length: 10,
				value: 'difficult',
			},
			
			course: {
				type: 'varchar',
				length: 50,
				value: 'course',
			},
			
			couisine: {
				type: 'varchar',
				length: 50,
				value: 'cousine',
			},
		},
		
		index: {
			title: ['title', 'description']
		}
	}
}