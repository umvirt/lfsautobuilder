dirs:
	mkdir -v build src/packages tmp
	chmod -v 777 build src/packages tmp

doc: 
	markdown README.md > README.html
	markdown SYSV.md > SYSV.html
