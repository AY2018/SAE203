

/* Project Galleries */

let galleryList = document.getElementById("galleryList");
let galleryIcons = document.getElementById("galleryIcons");

function listAppear() {
  let galleryList = document.getElementById("galleryList");
  let galleryIcons = document.getElementById("galleryIcons");
  let deleteBtn = document.getElementById("deleteBtn");

  galleryList.style.display = "flex";
  galleryIcons.style.display = "none";
  deleteBtn.style.display = "flex";
  
}

function iconsAppear() {
  let galleryList = document.getElementById("galleryList");
  let galleryIcons = document.getElementById("galleryIcons");
  let deleteBtn = document.getElementById("deleteBtn");



  galleryList.style.display = "none";
  galleryIcons.style.display = "flex";
  deleteBtn.style.display = "none";
}

/* Text area */

const textarea = document.querySelector('#description');

textarea.addEventListener('click', function() {
  this.selectionStart = 0;
});


/* Traces */


function closeTraces(){
  let trace = document.getElementById('tracesShowcase');
  trace.classList.add('traceDisapear');
  trace.classList.remove('traceAppear');
}

function openTraces(){
  let trace = document.getElementById('tracesShowcase');
  trace.classList.add('traceAppear');
  trace.classList.remove('traceDisapear');
}

function openSupprimer(){
  let supp = document.getElementById('SupprimerArticle');
  supp.classList.add('traceAppear');
  supp.classList.remove('traceDisapear');
}

function closeSupprimer(){
  let supp = document.getElementById('SupprimerArticle');
  supp.classList.add('traceDisapear');
  supp.classList.remove('traceAppear');
}


function afficherSupp(){
  var checkboxes = document.getElementsByClassName('checkbox');

  // Loop through the checkboxes and change their display style to flex
  for(var i = 0; i < checkboxes.length; i++) {
    checkboxes[i].style.display = 'block';
  }
}



function closepdp(){
  
  let changeImg = document.getElementById('changeImg');
  changeImg.style.display = "none";
}

function openpdp(){
  
  let changeImg = document.getElementById('changeImg');
  changeImg.style.display = "block";
}