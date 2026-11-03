// Confirm Delete

document.querySelectorAll(".delete-btn").forEach(btn=>{

btn.addEventListener("click",function(e){

if(!confirm("Bạn chắc chắn muốn xóa?")){

e.preventDefault();

}

});

});

// Bootstrap Tooltip

const tooltipTriggerList=[].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))

tooltipTriggerList.map(function (tooltipTriggerEl){

return new bootstrap.Tooltip(tooltipTriggerEl)

})