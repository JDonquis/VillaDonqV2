const slider_parent = document.querySelector(".slider");
const slider_wrap = document.querySelector(".slider_wrap")
const each_slider = slider_wrap.querySelectorAll(".each_slider_element")
const prev_btn = document.querySelector(".prev")
const next_btn = document.querySelector(".next")
const nav_slider = document.querySelector(".nav_slider")
const progress_bar = document.querySelector(".progress_bar div")

let default_act = [...each_slider].findIndex(el => el.classList.contains('default_act'))
if (default_act === -1 ) default_act = 0

const n_sliders = slider_wrap.childElementCount

let per_view = 1
let per_move = 1
let opacity_effect_each = false;
let automatic_move = false; 
let fade_effect = false;
let zoom_effect_each = false;
let type_form = true;

slider_wrap.style.gridTemplateColumns = `repeat(${n_sliders}, ${100 / per_view}%)`

const style = getComputedStyle(slider_wrap)
let gap = style.gap.replace(/\D/g, '')

const each_width = each_slider[0].offsetWidth + +gap;

let active_slider = default_act
// nav slider
const formule = (n_sliders - per_view)
let li_active = default_act

slider_wrap.scrollLeft = each_width * default_act

if (nav_slider) {

    // if ( Math.round(n_sliders/per_move)%2 == 0 ) i = -per_move
    // console.log(i);
    for (let i = 0; i < formule + 1; i++) {
        let li = document.createElement('li')
        li.dataset.n = i
        nav_slider.appendChild(li);
    }
    var all_li_nav = nav_slider.querySelectorAll('li')

    function liActivate() {
        all_li_nav.forEach(li => li.classList.remove('active'))
        all_li_nav[active_slider].classList.add('active')
    }
    liActivate()
    //event on nav list
    nav_slider.childNodes.forEach(li => {
        li.onclick = () => {
            const position = li.dataset.n
            position > active_slider ? translate('>', position) : translate('<', position)
        }
    })
}
if (next_btn || prev_btn) {
    next_btn.onclick = () => translate('>', active_slider + 1)
    prev_btn.onclick = () => translate('<', active_slider - 1)

    //this fuction is for style the buttons next or prev when there is not more sections to go 
    function btnOpacity() {
        active_slider == 0 ? prev_btn.classList.add('disabled') : prev_btn.classList.remove('disabled')
        active_slider == n_sliders - per_view ? next_btn.classList.add('disabled') : next_btn.classList.remove('disabled')
    }
    btnOpacity()
}

if (progress_bar) {
    // set the width of the progress bar
    function progress() {
        progress_bar.style.width = `${((active_slider + 1) / n_sliders) * 100}%`
    }
    progress()
}


let scroll_x_position = slider_wrap.scrollLeft;
if (slider_parent.classList.contains('fade_effect')) fade_effect = true;
if (slider_parent.classList.contains('zoom_effect')) zoom_effect_each = true
if (slider_parent.classList.contains('auto')) automatic_move = true
if (slider_parent.classList.contains('opacity_effect_each'))  opacity_effect_each = true
if (slider_parent.classList.contains('type_form'))  type_form = true


function translate(direction, times = 1) {
    function move() {
        active_slider = times
        if (fade_effect == true) {
            each_slider.forEach(i => i.style.opacity = '0')
            each_slider[active_slider].style.opacity = '1'
        } else {
            console.log(slider_wrap)
            let new_scroll_x_position =  each_width * times
            slider_wrap.scrollLeft = new_scroll_x_position
            
        }
        // console.log(slider_wrap)
        scroll_x_position = each_width * times;
        // console.log({scroll_x_position})
        if(zoom_effect_each == true) {
            // each_slider.forEach(e => e.classList.remove('zoom_effect_act'))
            each_slider[active_slider].classList.add('zoom_effect_act')
            each_slider[active_slider+1]?.classList.remove('zoom_effect_act')
            each_slider[active_slider-2]?.classList.remove('zoom_effect_act')

        }


    }

    if (direction == '>') {
        if (active_slider < formule) move()
    }

    if (direction == '<') {
        if (active_slider > 0) move()
    }

    if (opacity_effect_each) {
        each_slider.forEach(e => e.style.opacity = '0')
        each_slider[active_slider].style.opacity = '1'
    }
    btnOpacity?.()
    liActivate?.()
    progress?.()


}





var less_than_px = window.matchMedia("(max-width: 700px)")
if (less_than_px.matches) {

    let swift_scroll = false;
    let swift_move = false
    
    slider_wrap.addEventListener('touchstart',  () => {
        swift_move = true
        swift_scroll = false
    }, {passive: true})
    
    slider_wrap.addEventListener('touchend', e => {
        if (swift_scroll == false) {
            startDebounce()
        }
        swift_move = false
    })
    
    slider_wrap.addEventListener('scroll', e => {
        if (swift_move == false) {
            swift_scroll = true;
            startDebounce()
        }
    })
    
    
    function debounce(fn, delay = 50) {
        let time;
        return function (...arg) {
            clearTimeout(time)
            time = setTimeout(() => {
                fn(...arg)
            }, delay);
        }
    }
    
    function MoveAfterScroll() {
        let round_slider = Math.round(slider_wrap.scrollLeft / (each_width))
        slider_wrap.scrollLeft > scroll_x_position ? translate('>', round_slider) : translate('<', round_slider)
    }
    let startDebounce = debounce(MoveAfterScroll)
    
    
}
// automatic change
if (automatic_move== true) {
    setInterval(() => {
        if (active_slider == n_sliders-1) active_slider = -1
        translate('>', active_slider+1)
    }, 5000);
}


//change to the focused input
// console.log(slider_wrap)
if (type_form == true){
    each_slider.forEach((el,i) => {
        el.addEventListener('focusin', (e) => {
            if (active_slider != i ) {
                i > active_slider ? translate('>', i) : translate('<', i)
            }
        })
    })
}