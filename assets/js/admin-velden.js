/* Media-kiezer voor de eigen velden (afbeelding en fotogalerij). */
(function ($) {
	function bijwerkenGalerij($box) {
		var ids = $box.find('.bdj-preview li').map(function () { return $(this).data('id'); }).get();
		$box.find('input[type=hidden]').val(ids.join(','));
	}

	$(document).on('click', '.bdj-kies', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.bdj-afbeelding');
		var meerdere = $box.data('multiple') === 1;
		var frame = wp.media({
			title: meerdere ? "Foto's kiezen" : 'Afbeelding kiezen',
			button: { text: 'Gebruiken' },
			library: { type: 'image' },
			multiple: meerdere ? 'add' : false
		});

		frame.on('select', function () {
			var selectie = frame.state().get('selection').toJSON();
			if (meerdere) {
				var $lijst = $box.find('.bdj-preview');
				selectie.forEach(function (a) {
					if ($lijst.find('li[data-id="' + a.id + '"]').length) return;
					var url = (a.sizes && a.sizes.thumbnail ? a.sizes.thumbnail.url : a.url);
					$lijst.append('<li data-id="' + a.id + '"><img src="' + url + '" alt=""><button type="button" class="bdj-weg" aria-label="Verwijderen">×</button></li>');
				});
				bijwerkenGalerij($box);
			} else {
				var a = selectie[0];
				var url = (a.sizes && a.sizes.medium ? a.sizes.medium.url : a.url);
				$box.find('input[type=hidden]').val(a.id);
				$box.find('.bdj-preview').html('<img src="' + url + '" alt="">');
				$box.find('.bdj-wis').prop('hidden', false);
			}
		});
		frame.open();
	});

	$(document).on('click', '.bdj-wis', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.bdj-afbeelding');
		$box.find('input[type=hidden]').val('');
		$box.find('.bdj-preview').empty();
		$(this).prop('hidden', true);
	});

	$(document).on('click', '.bdj-weg', function (e) {
		e.preventDefault();
		var $box = $(this).closest('.bdj-afbeelding');
		$(this).closest('li').remove();
		bijwerkenGalerij($box);
	});

	$(function () {
		$('.bdj-galerij .bdj-preview').sortable({
			update: function () { bijwerkenGalerij($(this).closest('.bdj-afbeelding')); }
		});
	});
})(jQuery);
