$(document).ready(function () {
	// Show modal action
	$('#callback-modal').on('show.bs.modal', function (e) {
		$('#callback-show').hide(500);
		$('#callback-url').val(window.location.href);
	});
	// Hide modal action
	$('#callback-modal').on('hide.bs.modal', function (e) {
		$('#callback-show').show(500);
		document.getElementById('callback-form').reset();
		$('.form-group').removeClass('has-success').removeClass('has-error');
	});
	// Store data for callback
	$('#callback-store').on('click', function (e) {
		e.preventDefault();
		if (validateForm()) storeCallback();
	});

	// Function for store data callback to db
	function storeCallback() {
		const button = $('#callback-store');
		const url = $('#callback-form').attr('action');
		const formData = $('#callback-form').serialize();

		lockButtons();
		button.find('i.fa').removeClass('fa-paper-plane').addClass('fa-spinner fa-pulse');

		$.ajax({
			type: 'post',
			url: url,
			data: formData,
			dataType: 'json',
			success: function (response) {
				if (response.status === 'success') {
					$('#callback-modal').modal('hide');
					$('#callback-alert').modal('show');
				} else if (response.status === 'error') {
					if (response.errors.phone) {
						$('#callback-phone').parents('.form-group').addClass('has-error');
					}
					if (response.errors.name) {
						$('#callback-name').parents('.form-group').addClass('has-error');
					}
				}
			},
			error: function (xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			},
			complete: function () {
				unLockButtons();
				button.find('i.fa').removeClass('fa-spinner fa-pulse').addClass('fa-paper-plane');
			}
		});
	}

	// Lock/Unlock buttons
	function lockButtons() {
		$('.modal-footer button').prop('disabled', true);
	}
	function unLockButtons() {
		$('.modal-footer button').prop('disabled', false);
	}

	// Validate
	function validateForm() {
		let errors = 0;

		let name = $('#callback-name');
		name.parents('.form-group').find('.help-block').remove();
		if (validateName(name.val())) {
			name.parents('.form-group').addClass('has-success').removeClass('has-error');
		} else {
			errors++;
			name.parents('.form-group').addClass('has-error').removeClass('has-success');
		}

		let phone = $('#callback-phone');
		if (validatePhone(phone.val())) {
			phone.parents('.form-group').addClass('has-success').removeClass('has-error');
		} else {
			errors++;
			phone.parents('.form-group').addClass('has-error').removeClass('has-success');
		}

		let email = $('#callback-email');
		if (email.val() !== '') {
			if (validateEmail(email.val())) {
				email.parents('.form-group').addClass('has-success').removeClass('has-error');
			} else {
				errors++;
				email.parents('.form-group').addClass('has-error').removeClass('has-success');
			}
		} else {
			email.parents('.form-group').removeClass('has-error').removeClass('has-success');
		}

		return errors === 0;
	}
	function validateName(name) {
		return name.length >= 3;
	}
	function validatePhone(phone) {
		let regexp = /^[+]*[(]{0,1}[0-9]{1,3}[)]{0,1}[-\s\./0-9]*$/g;
		return regexp.test(phone);
	}
	function validateEmail(email) {
		let regexp = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,10})?$/;
		return regexp.test(email);
	}
});
