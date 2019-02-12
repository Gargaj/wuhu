var input = document.getElementById('votekey');
if (input && input.parentNode) {
	input.style.marginRight = '24px';

	var parentNode = input.parentNode;

	var container = document.createElement('span');
	container.style.position = 'relative';
	container.appendChild(input);

	var label = document.createElement('label');
	label.style.position = 'absolute';
	label.style.display = 'block';
	label.style.overflow = 'hidden';
	label.style.right = label.style.top = label.style.margin = '0px';
	label.style.width = '24px';
	label.style.height = '100%';
	label.style.cursor = 'pointer';
	label.style.backgroundImage = "url('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADwAAAA8AQAAAAASh+TfAAAAAnRSTlMAAQGU/a4AAABLSURBVHgBnc+BBcBQEAPQbJCROnwXygAl/YRCSa8/4OHEBU7UIIAAOmzS+ovrIM+G7ebBz9PtKx02JX1i5UEGviEp6D0dQI4LBttvueg4QNcDIJIAAAAASUVORK5CYII=')";
	label.style.backgroundSize = 'contain';
	label.style.backgroundRepeat = 'no-repeat';
	label.style.backgroundPosition = 'center';

	var file = document.createElement('input');
	file.style.position = 'absolute';
	file.style.left = '24px';
	file.style.opacity = '0';
	file.style.overflow = 'hidden';
	file.type = 'file';
	file.accept = 'image/*';
	file.capture = 'environment';
	file.tabindex = '-1';
	file.addEventListener('change', function(event) {
		if (!file.files || file.files.length === 0) { return; }

		var reader = new FileReader();
		reader.addEventListener('load', function(event) {
			file.value = '';
			qrcode.callback = function(result) {
				if (result instanceof Error) {
					if (errorMessage) {
						alert(errorMessage);
					}
				} else {
					input.value = result;
				}
			};
			qrcode.decode(reader.result);
		});
		reader.readAsDataURL(file.files[0]);
	});
	label.appendChild(file);

	container.appendChild(label);

	parentNode.appendChild(container);
}

