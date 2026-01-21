 "use strict";

 // CI4/JS hardening: don't crash if the mCustomScrollbar plugin isn't loaded
 if (typeof jQuery !== 'undefined' && jQuery.fn && typeof jQuery.fn.mCustomScrollbar === 'function') {
   jQuery("#right_column .makeScroll,.media_scroll,#activecampaign-list-group .makeScroll").mCustomScrollbar({
     autoHideScrollbar: true,
     theme: "rounded-dark"
   });
 }