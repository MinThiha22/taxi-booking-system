/*
	Author: Min Thiha Ko Ko
  ID: 21156028
  network username: sss0276
	Description: This script handles resetting the database.
*/

// Fetch post data to the server
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