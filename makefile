dirs:
	mkdir -v {build,src{,/books,/packages},tmp}
	chmod -v 777 {build,src{,/books,/packages},tmp}

doc: 
	markdown README.md > README.html
