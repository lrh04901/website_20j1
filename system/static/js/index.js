console.log("菜单按钮事件");
console.log("制作人:20计1 25 陆睿涵");
let burger = document.querySelector(".burger");
let navMenu = document.querySelector(".nav-list");
let navMenuItems = document.querySelectorAll(".nav-list li");
let widthHr = document.querySelector(".triangle");
// console.info(burger);
burger.addEventListener("click", function ()  {
    burger.classList.toggle("active");
    navMenu.classList.toggle("open");
    navMenuItems.forEach((item, index) => {
        if (item.style.animation) {
            item.style.animation = "";
        } else {
            item.style.animation = `0.3s ease-in slideIn forwards ${index *
            0.1 +
            0.3}s`;
        }
    });
    /*widthHr.style.width = 250px;*/
},false);

/*$(document).ready(function () {
   $(".burger").click(function () {
       $(this).toggleClass("active");
       $(".nav-link").toggleClass("open");

   });
});*/




