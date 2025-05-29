const postData = (dataSource, divID, drop) =>  {
	const place = document.getElementById(divID);
  const formData = new FormData();
  formData.append('drop', drop)
	const requestPromise = fetch(dataSource,{
		method: 'POST',
		body: formData
	});
	requestPromise.then(
		(response) => {
			response.text().then((text) => {
				place.innerHTML = text;
        place.scrollIntoView({ behavior: 'smooth' });
			});
		}
	).catch((error) => {
    place.innerHTML = 'Error: ' + error.message;
  });
};