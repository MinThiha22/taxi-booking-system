const getCurrentDate = () => {
	const now = new Date();
	return now.toISOString().split('T')[0];
}
const getCurrentTime = () => {
	const now = new Date();
	return now.toTimeString().slice(0,5);
}

const updateDateTime = () => {
  document.getElementById('pickup-date').value = getCurrentDate();
  document.getElementById('pickup-time').value = getCurrentTime();
}

updateDateTime();

document.getElementById('pickup-date').addEventListener('change', function() {
  const selectedDate = this.value;
	const currentDate = getCurrentDate();
  if (selectedDate && selectedDate < currentDate) {
    showModal('Invalid Date', 'Pick-up date must be in the future.');
    updateDateTime();
  }
});

document.getElementById('pickup-time').addEventListener('change', function() {
	const selectedTime = this.value;
	const selectedDate = document.getElementById('pickup-date').value;
	const currentDate = getCurrentDate();
	const currentTime = getCurrentTime();
	if (selectedDate == currentDate && selectedTime < currentTime) {
		showModal('Invalid Time', 'Pick-up time must be in the future.');
		updateDateTime();
	}
});

document.getElementById('booking-form').addEventListener('submit', (event) => {
  event.preventDefault();
  const form = event.target;

  if (!form.checkValidity()) {
    form.reportValidity();
    return;
  }
	const selectedDate = form['date'].value;
	const selectedTime = form['time'].value;
	const currentDate = getCurrentDate();
	const currentTime = getCurrentTime();

	if (selectedDate && selectedDate < currentDate) {
    showModal('Invalid Date', 'Pick-up date must be in the future.');
    updateDateTime();
		return
  }
	if (selectedDate == currentDate && selectedTime < currentTime) {
		showModal('Invalid Time', 'Pick-up time must be in the future.');
		updateDateTime();
		return;
	}
  const formData = new FormData(form);
  const formattedDate = selectedDate.split('-').reverse().join('/');
  formData.set('date', formattedDate);
  postData('booking.php', 'reference', formData);
	form.reset();
	updateDateTime();
});

const postData = (dataSource, divID, formData) =>  {
	const place = document.getElementById(divID);
	const parent = document.getElementById
	const requestPromise = fetch(dataSource,{
		method: 'POST',
		body: formData
	});
	requestPromise.then(
		(response) => {
			response.text().then((text) => {
				place.parentElement.removeAttribute('hidden');
				place.removeAttribute('hidden');
				place.innerHTML = text;
				place.scrollIntoView({behavior: 'smooth'});
			});
		}
	).catch((error) => {
    place.innerHTML = 'Error: ' + error.message;
  });;
} 

const showModal = (title, message) => {
	const dialog = document.querySelector('dialog');
	document.getElementById('modal-title').textContent = title;
	document.getElementById('modal-message').textContent = message;
	dialog.showModal();
};

setInterval(updateDateTime, 60 * 1000);