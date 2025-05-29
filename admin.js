const getCurrentDate = () => {
	const now = new Date();
	const year = now.getFullYear();
	const month = String(now.getMonth() + 1).padStart(2, '0');
	const day = String(now.getDate()).padStart(2, '0');
	return `${year}-${month}-${day}`;
}
const getCurrentTime = () => {
	const now = new Date();
	return now.toTimeString().slice(0,5);
}
document.getElementById('search-btn').addEventListener('click', () => {
  const form = document.getElementById('search-form');
  if (!form.checkValidity()) {
    form.reportValidity();
  } else {
	  const formData = new FormData(form);
	  formData.append('time', getCurrentTime());
	  formData.append('date', getCurrentDate());
	  postData('admin.php', 'content', formData);
  }
});

const postData = (dataSource, divID, formData) =>  {
	const place = document.getElementById(divID);
	const requestPromise = fetch(dataSource,{
		method: 'POST',
		body: formData
	});
	requestPromise.then(
		(response) => {
			response.text().then((text) => {
				place.innerHTML = text;
        assignButtonsEventListeners();
			});
		}
	).catch((error) => {
    place.innerHTML = 'Error: ' + error.message;
  });
}

const assignButtonsEventListeners = () => {
  const assignButtons = document.querySelectorAll('.assign-btn');
  assignButtons.forEach(btn => {
    btn.addEventListener('click', function() {
      const booking_ref = this.getAttribute('data-booking-ref');
      const formData = new FormData();
      formData.append('booking_ref', booking_ref);

      postData('admin.php', 'content', formData);
    })
  })
}

