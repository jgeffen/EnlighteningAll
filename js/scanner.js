const codeReader = new ZXing.BrowserMultiFormatReader();

codeReader.decodeFromVideoDevice(null, 'preview-video', (result, err) => {
	if (result) {
		console.log('Scanned UPC:', result.getText());
	}
	if (err && !(err instanceof ZXing.NotFoundException)) {
		console.error(err);
	}
});