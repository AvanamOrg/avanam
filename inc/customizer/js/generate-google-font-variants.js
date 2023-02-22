/**
 * manually run this file to generate array for google fonts inc/customizer/google-font-variants.php file)
 * run the script on a page with a div with class .google-fonts class.
 * you'll also need to put in a google webfonts api key.
 */ 
$.getJSON('https://www.googleapis.com/webfonts/v1/webfonts?key={{API_KEY}}', function(data) {
  $.each( data.items, function( index, font ) {
    var category = [];
    category.push( font.category );
    var variants = font.variants;
    var weights = font.variants.slice();
    var styles = ['normal'];
    for( var i = 0; i < weights.length; i++){ 
      if ( weights[i].includes('italic') ) {
        weights.splice(i, 1);
      }
    };
    if ( variants.includes('italic') ) {
        styles.push('italic');
     }
    $('.google-fonts').append("'" + font.family + "' => array( 'v' => array(");
    for(var i = 0; i < variants.length; i++) {
      if( 0 === i ) {
        $('.google-fonts').append("'" + variants[i] + "'");
      } else {
         $('.google-fonts').append(",'" + variants[i] + "'");
      }
    }
     $('.google-fonts').append(")" + ",'c' => array(");
    for(var i = 0; i < category.length; i++) {
      if( 0 === i ) {
        $('.google-fonts').append("'" + category[i] + "'");
      } else {
         $('.google-fonts').append(",'" + category[i] + "'");
      }
    }
     $('.google-fonts').append(")" + "),");
  });
});