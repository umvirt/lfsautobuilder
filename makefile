dirs:
	mkdir -v {builddir,books,src,tmp,log}
	chmod -v 777 builddir books src tmp log

doc: 
	markdown README.md > README.html
