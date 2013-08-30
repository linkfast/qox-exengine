// AjaxCore2 Basic Functions
// Requires AjaxObjects / AjaxObjects Lite / ExEngine Basic Javascript (automatic loaded with AC2_VisualWeb)

// http://mentaljetsam.wordpress.com/2008/06/02/using-javascript-to-post-data-between-pages/
// USE : javascript:ac2_postwith('post.php',{user:'peter',cc:'aus'}
function ac2_postwith (to,p) {
  var myForm = document.createElement("form");
  myForm.method="post" ;
  myForm.action = to ;
  for (var k in p) {
    var myInput = document.createElement("input") ;
    myInput.setAttribute("name", k) ;
    myInput.setAttribute("value", p[k]);
    myForm.appendChild(myInput) ;
  }
  document.body.appendChild(myForm) ;
  myForm.submit() ;
  document.body.removeChild(myForm) ;
}