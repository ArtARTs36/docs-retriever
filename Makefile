ifneq ("$(wildcard .env)","")
	include .env
	export
endif

try:
	./bin/docs-retriever
