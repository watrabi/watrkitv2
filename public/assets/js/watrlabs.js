// Copyright 2025 watrlabs
// if you break it then sd;ojgksdflkhgsgdfhjkl

const sidebar = document.getElementById("sidebar");
const sidebartoggle = document.getElementById("sidebar-nav");
const mainelement = document.getElementById("main");
const header = document.getElementById("header");
const footer = document.getElementById("footer");

ishidden = sidebar.classList.contains('side-hidden');

function toggleSideBar(){
    if(ishidden){
        sidebar.classList.remove("side-hidden");
        mainelement.style.paddingLeft = "250px";
        footer.style.paddingLeft = "250px";
        header.style.paddingLeft = "250px";
        ishidden = sidebar.classList.contains('side-hidden'); // redo it so the user cant break it
    } else {
        sidebar.classList.add("side-hidden");
        mainelement.style.paddingLeft = "0px";
        footer.style.paddingLeft = "0px";
        header.style.paddingLeft = "0px";
        ishidden = sidebar.classList.contains('side-hidden'); 
    }
    
}

sidebartoggle.addEventListener('click', function() {
  toggleSideBar();
});

function showmodal(content){
    const modalbg = document.createElement("div");
    modalbg.classList.add("modal-bg");
    
    const modalcontents = document.createElement("div");
    modalcontents.classList.add("center");
    modalcontents.innerHTML += content
    modalbg.appendChild(modalcontents);
    
    document.body.appendChild(modalbg);
}