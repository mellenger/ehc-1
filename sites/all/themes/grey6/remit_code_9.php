<?php
/**
 *	the EHC Remittance Form
 **/

//this is not needed because it's in the Drupal.settings js now
//if($previous_sizes){print $previous_sizes;}

profile_load_profile($user);
$provinces = array(
	'AB' => 'Alberta',
	'BC' => 'British Columbia',
	'MB' => 'Manitoba',
	'NB' => 'New Brunswick',
	'NL' => 'Newfoundland And Labrador',
	'NS' => 'Nova Sotia',
	'NT' => 'Northwest Territories',
	'NU' => 'Nunavt',
	'ON' => 'Ontario',
	'PE' => 'Prince Edward Island',
);

?>

<script type="text/javascript" charset="utf-8">

	var fieldsArray = [];
	//oil liquid
	fieldsArray.push( 'edit-submitted-oils-oil-fieldset-total-litres' );
	//filters
	fieldsArray.push( 'edit-submitted-filters-filters-8-fieldset-less-than-8-inches' );
	fieldsArray.push( 'edit-submitted-filters-filters--8-fieldset-greater-than-8-inches' );
//	fieldsArray.push( 'edit-submitted-filters-filters-sump-fieldset-sump-type' );
	//oil containers
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-500-ml' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-2-1-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-3-4-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-4-10-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-5-18-942-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-6-20-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-0947-l-0947-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-3788-l-3788-liter' );
	fieldsArray.push( 'edit-submitted-containers-size-wrapper-5-l-5-liter' );
	//glycol liquid
	fieldsArray.push( 'edit-submitted-glycol-concentrate-fieldset-concentrate-total-litres' );
	fieldsArray.push( 'edit-submitted-glycol-premix-fieldset-premix-total-litres' );
	//glycol containers
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-1000-ml-1000-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-1500-ml-1500-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-1890-ml-1890-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-3780-ml-3780-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-4000-ml-4000-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-5000-ml-5000-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-9460ml-9460-ml' );
	fieldsArray.push( 'edit-submitted-glycol-containers-antifreeze-wrapper-18900-ml-18900-ml' );

	function setup_defaults() {
		// Remove warning message.
		$('.error-warning').remove();

		var provinceSelector = $("#edit-submitted-province-select-province");
		var currentProvince;
		<?php if (isset($user->profile_province)) : ?>
			<?php foreach ($provinces as $key => $value): ?>
				<?php if ($value == $user->profile_province): ?>
					currentProvince = "<?php print $key; ?>";
					$(provinceSelector).val(currentProvince);
				<?php endif; ?>
			<?php	endforeach; ?>
		<?php else : ?>
			currentProvince = $(provinceSelector).val();
		<?php endif; ?>

		// Get start and end date
		var start = $('input[type="image"]').eq(0).val();
		var end = $('input[type="image"]').eq(1).val();

		// Load rates
		var url = location.origin + Drupal.settings.basePath + 'json/resource-sheet/' + currentProvince + '/' + start + '/' + end;
		$.get(url, function(data) {

			currentRates = JSON.parse(data);
			if (currentRates.error) {
				$('#webform-component-end-date').nextAll().hide();
				$('#center h2').after('<div class="error-warning">A rate adjustment has occurred during the time period you defined. Please submit your values from <em>' + currentRates.start + '</em> to <em>' + currentRates.end + '</em></div>');
			}
			else {
				$('#webform-component-province').nextAll().show();

				//load rates
				//oil
				$("#" + fieldsArray[0] + "-rate").val(currentRates.oil).niceRates();
				//filters
				$("#" + fieldsArray[1] + "-rate").val(currentRates.oil_filter_small).niceRates();
				$("#" + fieldsArray[2] + "-rate").val(currentRates.oil_filter_large).niceRates();
	//			$("#" + fieldsArray[3] + "-rate").val(currentRates.oil_filter_small).niceRates();
				var start = 4;
				var currentOilContainer = currentRates.oil_containers;
				while (12 >= start) {
					var value = currentOilContainer;
					if (start == 4) { value = value * 0.5; }
					if (start == 6) { value = value * 4; }
					if (start == 7) { value = value * 10; }
					if (start == 8) { value = value * 18.942; }
					if (start == 9) { value = value * 20; }
					if (start == 10) { value = value * 0.947; }
					if (start == 11) { value = value * 3.788; }
					if (start == 12) { value = value * 5; }
					$("#" + fieldsArray[start] + "-rate").val(value).niceRates();

					start++;
				}
				//concentrate
				$("#" + fieldsArray[13] + "-rate").val(currentRates.glycol_concentrate).niceRates();
				//premix
				$("#" + fieldsArray[14] + "-rate").val(currentRates.glycol_premix).niceRates();
				//containers
				var start = 15;
				//containers
				while (22 >= start) {
					//get the glycol container rate from the provincial rates array
					var value = currentRates.glycol_containers;
					//multiply by the rate
					if (start == 15) { value = value * 1; }
					if (start == 16) { value = value * 1.5; }
					if (start == 17) { value = value * 1.89; }
					if (start == 18) { value = value * 3.78; }
					if (start == 19) { value = value * 4; }
					if (start == 20) { value = value * 5; }
					if (start == 21) { value = value * 9.46; }
					if (start == 22) { value = value * 18.9; }
					//push into form
					$("#" + fieldsArray[start] + "-rate").val(value).niceRates();
					start++;
				}

				if (currentRates.hst != 0) {
					$("#edit-submitted-totals-tax-rate").val(currentRates.hst);
					$("#edit-submitted-totals-tax-wrapper").find('label').text('HST @ ' + (currentRates.hst*100) + '%');
				} else {
					var tax = (currentRates.pst + currentRates.gst);
					$("#edit-submitted-totals-tax-rate").val(tax);
					$("#edit-submitted-totals-tax-wrapper").find('label').text('GST @ ' + Math.round(tax * 100) + '%');
				}

			}
		}, "json");
	}

	function checkReady() {
		if (checkSheetDates()) {
			setup_defaults()
		}
	}

</script>

<script type="text/javascript" charset="utf-8">
/**
 *	the initialization
**/

$(document).ready( function () {
	// Hide the form until a date is set
	$('#webform-component-end-date').nextAll().hide();
  checkReady();

  jQuery.datepicker._defaults.onClose = function () {
		checkReady();
	};



	for (var field in fieldsArray) {
		$("#" + fieldsArray[field]).change( function () {
			var thisID = $(this).attr('id');

			//clear out any non-numbers
			$("#" + thisID).cleannum();
			var total = $("#" + thisID).val() * $("#" + thisID + "-rate").val();
			//add commas back
			$("#" + thisID).digits();

			if (check_decimals(total, 2, 9999)) {
				total = roundNumber(total, 2);
			}
			total = numberToFixed(total, 2);
			$("#" + thisID + "-remittance").val(total);

			do_totals();
			$("#" + thisID + "-remittance").digits();
		});
	}

	//the tax applicable sales and admin fees
	$('#edit-submitted-totals-tax-applicable-sales, #edit-submitted-totals-interest-admin-fees').change(function(){
		//console.log($(this).val());
		 var total = $(this).cleannum().val();
		//console.log(total);

		do_totals();

		if (check_decimals(total, 2, 9999)) {
			total = roundNumber(total, 2);
		}
		total = numberToFixed(total, 2);

		$(this).val(total).digits();
		//console.log(total);
		//console.log($(this).val());
	});


});
</script>
