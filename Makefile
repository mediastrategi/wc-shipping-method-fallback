.PHONY: release
release:
	cd .. && zip -r wc-shipping-method-fallback.zip wc-shipping-method-fallback/ -x \*.git\* -x \*Makefile -x \*README.md
