(function ($, products, upsells, is_product_page, ajax_object, options) {
	/* Remove blue success banner */
	if (is_product_page) {
		var element = document.createElement("style"),
			sheet;
		document.head.appendChild(element);
		sheet = element.sheet;

		let rules = [];
		let cs = "div.woocommerce-notices-wrapper { display: none; }";
		rules.push(cs);
		let cs0 = "";
		let cs1 = "";
		let cs11 = "";
		if (options["id_primary_color"]) {
			cs1 += " .button-winkelmand { background-color: " + options["id_primary_color"] + " !important; }";
			cs0 +=
				".winkelwagen-popup-buttonverderwinkelen:hover SPAN { text-decoration-color: " +
				options["id_primary_color"] +
				" !important; }";
			cs11 +=
				".winkelwagen-popup-buttonverderwinkelen:hover .fa-times::before {color: " +
				options["id_primary_color"] +
				"; !important}";
			rules.push(cs1);
			rules.push(cs0);
			rules.push(cs11);
		}
		let cs2 = "";
		if (options["id_secondary_color"]) {
			cs2 += " .winkelwagen-popup-button { background-color: " + options["id_secondary_color"] + " !important; }";
			rules.push(cs2);
		}

		let cs3 = "";
		if (options["id_title_color"]) {
			cs3 += ".winkelwagen-popup-h4 { color: " + options["id_title_color"] + " !important}";
			rules.push(cs3);
		}

		let cs5 = "";
		if (options["id_upsell_title_color"]) {
			cs5 += ".winkelwagen-popup-title { color: " + options["id_upsell_title_color"] + " !important}";
			rules.push(cs5);
		}

		var i = 0;
		rules.forEach((rule) => {
			sheet.insertRule(rule, i);
			i++;
		});
	}

	class PopupBuilder {
		constructor() {
			this.html = "";
		}

		buildBeginWrapper() {
			this.html += '<div id="popup-wrapper">';
		}

		buildEndWrapper() {
			this.html += "</div>";
		}

		buildHeader(title) {
			this.html +=
				'<div class="row cart-header-wrapper">' +
				'<div class="col-md-12 cart-header-container">' +
				'<h4 class="winkelwagen-popup-h4">' +
				(options["id_title_content"] ? options["id_title_content"] : "Item added to your cart") +
				"</h4>" +
				'<button class="winkelwagen-popup-buttonverderwinkelen" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"> <span>' +
				(options["id_close_popup_content"] ? options["id_close_popup_content"] : "Continue shopping") +
				"</span></i></button>" +
				"</div>" +
				"</div>";
		}

		buildHeadProduct(product) {
			var options_locale = options["id_separator"] && options["id_separator"] == "decimal_comma" ? "nl-BE" : "en-US";
			this.html +=
				'<div class="row head-product-wrapper">' +
				'<div class="col-lg-2 col-md-6 col-sm-12 image-container">' +
				'<img src="' +
				product["image"] +
				'" width="100px"></img>' +
				"</div>" +
				'<div class="col-lg-3 col-md-6 col-sm-12 name-container">' +
				'<span class="winkelwagen-popup-name">' +
				product["name"] +
				"</span>" +
				"</div>" +
				'<div class="col-lg-1 col-md-6 col-sm-12 quantity-container">' +
				'<span class="winkelwagen-popup-name">x' +
				product["quantity"] +
				"</span>" +
				"</div>" +
				'<div class="col-lg-3 col-md-6 col-sm-12 price-container" style="">';

			if (options["id_excl_vat"] && options["id_excl_vat"] == "on") {
				this.html +=
					'<div class="winkelwagen-popup-price ex-btw"><span class="headProductPrice">€ ' +
					Number(product["price"] * product["quantity"]).toLocaleString(options_locale, {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2,
					}) +
					"</span> <span class='headProduct-ex-btw'>Excl. btw</span></div>";
			}

			if (options["id_incl_vat"] && options["id_incl_vat"] == "on") {
				this.html +=
					'<div class="winkelwagen-popup-price inc-btw"><span>€ ' +
					Number(product["price"] * product["quantity"] * 1.21).toLocaleString(options_locale, {
						minimumFractionDigits: 2,
						maximumFractionDigits: 2,
					}) +
					"</span> <span class='headProduct-inc-btw'>Incl. btw</span></div>";
			}

			this.html +=
				"</div>" +
				'<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' +
				'<button class="button-winkelmand"><a id="winkelwagenpopup-button-link" href="/winkelmand">' +
				(options["id_goto_cart_content"] ? options["id_goto_cart_content"] : "Go to cart") +
				'</a><i class="fa fa-arrow-right"></i></button>' +
				"</div>" +
				"</div>";
		}

		buildGroupedProducts(products) {
			products.forEach((product) => {
				this.html +=
					'<div class="row other-product-wrapper">' +
					'<div class="col-lg-2 col-md-6 col-sm-12 other-image-container">' +
					'<img src="' +
					product["image"] +
					'" width="100px"></img>' +
					"</div>" +
					'<div class="col-lg-3 col-md-6 col-sm-12 other-name-container">' +
					'<span class="winkelwagen-popup-name">' +
					product["name"] +
					"</span>" +
					"</div>" +
					'<div class="col-lg-1 col-md-6 col-sm-12 other-quantity-container">' +
					'<span class="winkelwagen-popup-name">x' +
					product["quantity"] +
					"</span>" +
					"</div>" +
					'<div class="col-lg-3 col-md-6 col-sm-12 other-price-container">';

				if (options["id_excl_vat"] && options["id_excl_vat"] == "on") {
					this.html +=
						'<div class="winkelwagen-popup-price ex-btw"><span class="otherProductPrice">€ ' +
						Number(product["price"] * product["quantity"]).toLocaleString(options_locale, {
							minimumFractionDigits: 2,
							maximumFractionDigits: 2,
						}) +
						"</span> <span class='otherProduct-ex-btw'>Excl. btw</span></div>";
				}

				if (options["id_incl_vat"] && options["id_incl_vat"] == "on") {
					this.html +=
						'<div class="winkelwagen-popup-price inc-btw"><span>€ ' +
						Number(product["price"] * product["quantity"] * 1.21).toLocaleString(options_locale, {
							minimumFractionDigits: 2,
							maximumFractionDigits: 2,
						}) +
						"</span> <span class='otherProduct-inc-btw'>Incl. btw</span></div>";
				}

				this.html +=
					"</div>" + '<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' + "</div>" + "</div>";
			});
		}

		buildUpsellTitle() {
			this.html +=
				'<div class="upsell-title-wrapper"><div class="col-md-12" style="text-align:center;"><h5 class="winkelwagen-popup-title">' +
				(options["id_upsell_title_content"]
					? options["id_upsell_title_content"]
					: "Customers who bought this also bought") +
				"</h5></div></div>" +
				'<div class="products row" style="align-items: center; justify-content: center;">';
		}

		buildUpsells(upsells) {
			var options_locale = options["id_separator"] && options["id_separator"] == "decimal_comma" ? "nl-BE" : "en-US";
			upsells.forEach((upsell) => {
				this.html +=
					'<div class="row upsell-wrapper">' +
					'<div class="col-lg-3 col-md-6 col-sm-12 upsell-image-container">' +
					'<img src="' +
					upsell["image"] +
					'" width="100px"></img>' +
					"</div>" +
					'<div class="col-lg-3 col-md-6 col-sm-12 upsell-name-container">' +
					'<span class="winkelwagen-popup-name"><a href="' +
					upsell["link"] +
					'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' +
					upsell["name"] +
					"</a></span>" +
					"</div>" +
					'<div class="col-lg-3 col-md-6 col-sm-12 upsell-price-container">';

				if (options["id_excl_vat"] && options["id_excl_vat"] == "on") {
					this.html +=
						'<div class="winkelwagen-popup-price ex-btw"><span class="upsellProductPrice">€ ' +
						Number(upsell["price"]).toLocaleString(options_locale, {
							minimumFractionDigits: 2,
							maximumFractionDigits: 2,
						}) +
						"</span> <span class='upsellProduct-ex-btw'>Excl. btw</span></div>";
				}

				if (options["id_incl_vat"] && options["id_incl_vat"] == "on") {
					this.html +=
						'<div class="winkelwagen-popup-price inc-btw"><span>€ ' +
						Number(upsell["price"] * 1.21).toLocaleString(options_locale, {
							minimumFractionDigits: 2,
							maximumFractionDigits: 2,
						}) +
						"</span> <span class='upsellProduct-inc-btw'>Incl. btw</span></div>";
				}
				this.html +=
					"</div>" +
					'<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12 upsell-atc-container"> ' +
					'<button class="winkelwagen-popup-button"><a href="?add-to-cart=' +
					upsell["id"] +
					'" data-open="0" data-quantity="1" class="button wp-element-button product_type_simple ajax_add_to_cart add_to_cart_button" data-product_id="' +
					upsell["id"] +
					'" aria-label="Toevoegen" rel="nofollow">' +
					(options["id_upsell_addtocart_content"] ? options["id_upsell_addtocart_content"] : "Add to cart") +
					"</a></button>" +
					"</div>" +
					"</div>";
			});
		}

		build(products, upsells) {
			this.html = "";
			this.buildBeginWrapper();
			this.buildHeader();
			this.buildHeadProduct(products.shift());
			this.buildGroupedProducts(products);
			this.buildUpsellTitle();
			this.buildUpsells(upsells);
			this.buildEndWrapper();
			return this.html;
		}
	}

	/* UI, open pop-up bootstrap */
	function openModal(products, upsells) {
		const body = new PopupBuilder();
		var html = body.build(products, upsells);

		bootstrap.showModal({
			title: "winkelmand",
			backdrop: true,
			body: html,
			footer:
				'<button type="submit" class="btn btn-primary save-btn" style="background-color: #1D97FF;">' +
				"save" +
				"</button>",
			onCreate: function (modal) {},
		});
	}
	//emd class

	//Wait until an element exists
	function waitForElm(selector) {
		return new Promise((resolve) => {
			if (document.querySelector(selector)) {
				return resolve(document.querySelector(selector));
			}

			const observer = new MutationObserver((mutations) => {
				if (document.querySelector(selector)) {
					resolve(document.querySelector(selector));
					observer.disconnect();
				}
			});

			observer.observe(document.body, {
				childList: true,
				subtree: true,
			});
		});
	}

	/* After DOM is ready */
	$(document).ready(function () {
		if ($(".primary-color-field").length > 0) {
			$(".primary-color-field").wpColorPicker();
		}

		if ($(".secondary-color-field").length > 0) {
			$(".secondary-color-field").wpColorPicker();
		}

		if ($(".title-color-field").length > 0) {
			$(".title-color-field").wpColorPicker();
		}

		if ($(".upsell-title-color-field").length > 0) {
			$(".upsell-title-color-field").wpColorPicker();
		}

		$(document).on("click", ".winkelwagen-popup-button", function () {
			$(this)
				.find(".button")
				.html(
					"<i class='fa fa-spinner'></i> " +
						(options["id_upsell_addedtocart_content"] ? options["id_upsell_addingtocart_content"] : "Adding..")
				);
			waitForElm(".winkelwagen-popup-button > .added_to_cart").then((elm) => {
				$(elm).hide();
				$(elm)
					.parent()
					.find(".button")
					.html(
						"<i class='fa fa-check'></i> " +
							(options["id_upsell_addedtocart_content"]
								? options["id_upsell_addedtocart_content"]
								: "Item added to your cart")
					);
			});
		});

		/* Open pop-up normal wp banner appears in dom */
		if ($(".woocommerce-message > a").length && is_product_page) {
			openModal(products, upsells);
			$(".modal-dialog").addClass("modal-lg");
			$(".modal-header").remove();
			$(".modal-footer").remove();
			var btn = $(this);
			//Clear session data after modal opens
			$.ajax({
				url: ajax_object.ajaxurl,
				type: "POST",
				data: {
					action: "clear_sesh",
				},
				success: function (data) {
					btn.submit();
				},
			});
		}

		//Clear session data when modal is closed
		$("body").on("hidden.bs.modal", ".modal", function () {
			$.ajax({
				url: ajax_object.ajaxurl,
				type: "POST",
				data: {
					action: "clear_sesh",
				},
				success: function (data) {},
			});
		});

		// Close pop-up when clicking outside of it
		$("body").on("click", ".modal", function (ev) {
			if (ev.target != $(this)[0]) return;
			$(".modal").modal("hide");
		});

		// Open pop-up on archive page via ajax
		$(".ajax_add_to_cart").click(function (ev) {
			if ($(this).data("open") != undefined) return;

			$.ajax({
				url: ajax_object.ajaxurl,
				type: "POST",
				data: {
					action: "get_productinfo",
					product_id: $(this).data("product_id"),
				},
				success: function (data) {
					data = JSON.parse(data);
					var product = {
						id: $(this).data("product_id"),
						image: data["image"],
						price: data["price"],
						name: data["name"],
						quantity: 1,
					};
					openModal([product], data["upsells"]);
					$(".modal-dialog").addClass("modal-lg");
					$(".modal-header").remove();
					$(".modal-footer").remove();
					var btn = $(this);
					$.ajax({
						url: ajax_object.ajaxurl,
						type: "POST",
						data: {
							action: "clear_sesh",
						},
						success: function (data) {
							btn.submit();
						},
					});
				},
			});
		});
	});
})(jQuery, products, upsells, is_product_page, ajax_object, options);
