dirs:
	mkdir -v build src/packages tmp
	chmod -v 777 build src/books src/packages tmp

doc: 
	markdown README.md > README.html
