<!doctype html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        LiftDeck | Elevate your coaching experience!
    </title>
    <link
        rel="shortcut icon"
        href="storage/images/favicon.png"
        type="image/x-icon"
    />
    <link rel="stylesheet" href="storage/css/swiper-bundle.min.css" />
    <link rel="stylesheet" href="storage/css/animate.css" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/animate.css', 'resources/js/input.css'])
    @else
        <style>
            /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */@layer theme{:root,:host{--font-sans:'Instrument Sans',ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";--font-serif:ui-serif,Georgia,Cambria,"Times New Roman",Times,serif;--font-mono:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace;--color-red-50:oklch(.971 .013 17.38);--color-red-100:oklch(.936 .032 17.717);--color-red-200:oklch(.885 .062 18.334);--color-red-300:oklch(.808 .114 19.571);--color-red-400:oklch(.704 .191 22.216);--color-red-500:oklch(.637 .237 25.331);--color-red-600:oklch(.577 .245 27.325);--color-red-700:oklch(.505 .213 27.518);--color-red-800:oklch(.444 .177 26.899);--color-red-900:oklch(.396 .141 25.723);--color-red-950:oklch(.258 .092 26.042);--color-orange-50:oklch(.98 .016 73.684);--color-orange-100:oklch(.954 .038 75.164);--color-orange-200:oklch(.901 .076 70.697);--color-orange-300:oklch(.837 .128 66.29);--color-orange-400:oklch(.75 .183 55.934);--color-orange-500:oklch(.705 .213 47.604);--color-orange-600:oklch(.646 .222 41.116);--color-orange-700:oklch(.553 .195 38.402);--color-orange-800:oklch(.47 .157 37.304);--color-orange-900:oklch(.408 .123 38.172);--color-orange-950:oklch(.266 .079 36.259);--color-amber-50:oklch(.987 .022 95.277);--color-amber-100:oklch(.962 .059 95.617);--color-amber-200:oklch(.924 .12 95.746);--color-amber-300:oklch(.879 .169 91.605);--color-amber-400:oklch(.828 .189 84.429);--color-amber-500:oklch(.769 .188 70.08);--color-amber-600:oklch(.666 .179 58.318);--color-amber-700:oklch(.555 .163 48.998);--color-amber-800:oklch(.473 .137 46.201);--color-amber-900:oklch(.414 .112 45.904);--color-amber-950:oklch(.279 .077 45.635);--color-yellow-50:oklch(.987 .026 102.212);--color-yellow-100:oklch(.973 .071 103.193);--color-yellow-200:oklch(.945 .129 101.54);--color-yellow-300:oklch(.905 .182 98.111);--color-yellow-400:oklch(.852 .199 91.936);--color-yellow-500:oklch(.795 .184 86.047);--color-yellow-600:oklch(.681 .162 75.834);--color-yellow-700:oklch(.554 .135 66.442);--color-yellow-800:oklch(.476 .114 61.907);--color-yellow-900:oklch(.421 .095 57.708);--color-yellow-950:oklch(.286 .066 53.813);--color-lime-50:oklch(.986 .031 120.757);--color-lime-100:oklch(.967 .067 122.328);--color-lime-200:oklch(.938 .127 124.321);--color-lime-300:oklch(.897 .196 126.665);--color-lime-400:oklch(.841 .238 128.85);--color-lime-500:oklch(.768 .233 130.85);--color-lime-600:oklch(.648 .2 131.684);--color-lime-700:oklch(.532 .157 131.589);--color-lime-800:oklch(.453 .124 130.933);--color-lime-900:oklch(.405 .101 131.063);--color-lime-950:oklch(.274 .072 132.109);--color-green-50:oklch(.982 .018 155.826);--color-green-100:oklch(.962 .044 156.743);--color-green-200:oklch(.925 .084 155.995);--color-green-300:oklch(.871 .15 154.449);--color-green-400:oklch(.792 .209 151.711);--color-green-500:oklch(.723 .219 149.579);--color-green-600:oklch(.627 .194 149.214);--color-green-700:oklch(.527 .154 150.069);--color-green-800:oklch(.448 .119 151.328);--color-green-900:oklch(.393 .095 152.535);--color-green-950:oklch(.266 .065 152.934);--color-emerald-50:oklch(.979 .021 166.113);--color-emerald-100:oklch(.95 .052 163.051);--color-emerald-200:oklch(.905 .093 164.15);--color-emerald-300:oklch(.845 .143 164.978);--color-emerald-400:oklch(.765 .177 163.223);--color-emerald-500:oklch(.696 .17 162.48);--color-emerald-600:oklch(.596 .145 163.225);--color-emerald-700:oklch(.508 .118 165.612);--color-emerald-800:oklch(.432 .095 166.913);--color-emerald-900:oklch(.378 .077 168.94);--color-emerald-950:oklch(.262 .051 172.552);--color-teal-50:oklch(.984 .014 180.72);--color-teal-100:oklch(.953 .051 180.801);--color-teal-200:oklch(.91 .096 180.426);--color-teal-300:oklch(.855 .138 181.071);--color-teal-400:oklch(.777 .152 181.912);--color-teal-500:oklch(.704 .14 182.503);--color-teal-600:oklch(.6 .118 184.704);--color-teal-700:oklch(.511 .096 186.391);--color-teal-800:oklch(.437 .078 188.216);--color-teal-900:oklch(.386 .063 188.416);--color-teal-950:oklch(.277 .046 192.524);--color-cyan-50:oklch(.984 .019 200.873);--color-cyan-100:oklch(.956 .045 203.388);--color-cyan-200:oklch(.917 .08 205.041);--color-cyan-300:oklch(.865 .127 207.078);--color-cyan-400:oklch(.789 .154 211.53);--color-cyan-500:oklch(.715 .143 215.221);--color-cyan-600:oklch(.609 .126 221.723);--color-cyan-700:oklch(.52 .105 223.128);--color-cyan-800:oklch(.45 .085 224.283);--color-cyan-900:oklch(.398 .07 227.392);--color-cyan-950:oklch(.302 .056 229.695);--color-sky-50:oklch(.977 .013 236.62);--color-sky-100:oklch(.951 .026 236.824);--color-sky-200:oklch(.901 .058 230.902);--color-sky-300:oklch(.828 .111 230.318);--color-sky-400:oklch(.746 .16 232.661);--color-sky-500:oklch(.685 .169 237.323);--color-sky-600:oklch(.588 .158 241.966);--color-sky-700:oklch(.5 .134 242.749);--color-sky-800:oklch(.443 .11 240.79);--color-sky-900:oklch(.391 .09 240.876);--color-sky-950:oklch(.293 .066 243.157);--color-blue-50:oklch(.97 .014 254.604);--color-blue-100:oklch(.932 .032 255.585);--color-blue-200:oklch(.882 .059 254.128);--color-blue-300:oklch(.809 .105 251.813);--color-blue-400:oklch(.707 .165 254.624);--color-blue-500:oklch(.623 .214 259.815);--color-blue-600:oklch(.546 .245 262.881);--color-blue-700:oklch(.488 .243 264.376);--color-blue-800:oklch(.424 .199 265.638);--color-blue-900:oklch(.379 .146 265.522);--color-blue-950:oklch(.282 .091 267.935);--color-indigo-50:oklch(.962 .018 272.314);--color-indigo-100:oklch(.93 .034 272.788);--color-indigo-200:oklch(.87 .065 274.039);--color-indigo-300:oklch(.785 .115 274.713);--color-indigo-400:oklch(.673 .182 276.935);--color-indigo-500:oklch(.585 .233 277.117);--color-indigo-600:oklch(.511 .262 276.966);--color-indigo-700:oklch(.457 .24 277.023);--color-indigo-800:oklch(.398 .195 277.366);--color-indigo-900:oklch(.359 .144 278.697);--color-indigo-950:oklch(.257 .09 281.288);--color-violet-50:oklch(.969 .016 293.756);--color-violet-100:oklch(.943 .029 294.588);--color-violet-200:oklch(.894 .057 293.283);--color-violet-300:oklch(.811 .111 293.571);--color-violet-400:oklch(.702 .183 293.541);--color-violet-500:oklch(.606 .25 292.717);--color-violet-600:oklch(.541 .281 293.009);--color-violet-700:oklch(.491 .27 292.581);--color-violet-800:oklch(.432 .232 292.759);--color-violet-900:oklch(.38 .189 293.745);--color-violet-950:oklch(.283 .141 291.089);--color-purple-50:oklch(.977 .014 308.299);--color-purple-100:oklch(.946 .033 307.174);--color-purple-200:oklch(.902 .063 306.703);--color-purple-300:oklch(.827 .119 306.383);--color-purple-400:oklch(.714 .203 305.504);--color-purple-500:oklch(.627 .265 303.9);--color-purple-600:oklch(.558 .288 302.321);--color-purple-700:oklch(.496 .265 301.924);--color-purple-800:oklch(.438 .218 303.724);--color-purple-900:oklch(.381 .176 304.987);--color-purple-950:oklch(.291 .149 302.717);--color-fuchsia-50:oklch(.977 .017 320.058);--color-fuchsia-100:oklch(.952 .037 318.852);--color-fuchsia-200:oklch(.903 .076 319.62);--color-fuchsia-300:oklch(.833 .145 321.434);--color-fuchsia-400:oklch(.74 .238 322.16);--color-fuchsia-500:oklch(.667 .295 322.15);--color-fuchsia-600:oklch(.591 .293 322.896);--color-fuchsia-700:oklch(.518 .253 323.949);--color-fuchsia-800:oklch(.452 .211 324.591);--color-fuchsia-900:oklch(.401 .17 325.612);--color-fuchsia-950:oklch(.293 .136 325.661);--color-pink-50:oklch(.971 .014 343.198);--color-pink-100:oklch(.948 .028 342.258);--color-pink-200:oklch(.899 .061 343.231);--color-pink-300:oklch(.823 .12 346.018);--color-pink-400:oklch(.718 .202 349.761);--color-pink-500:oklch(.656 .241 354.308);--color-pink-600:oklch(.592 .249 .584);--color-pink-700:oklch(.525 .223 3.958);--color-pink-800:oklch(.459 .187 3.815);--color-pink-900:oklch(.408 .153 2.432);--color-pink-950:oklch(.284 .109 3.907);--color-rose-50:oklch(.969 .015 12.422);--color-rose-100:oklch(.941 .03 12.58);--color-rose-200:oklch(.892 .058 10.001);--color-rose-300:oklch(.81 .117 11.638);--color-rose-400:oklch(.712 .194 13.428);--color-rose-500:oklch(.645 .246 16.439);--color-rose-600:oklch(.586 .253 17.585);--color-rose-700:oklch(.514 .222 16.935);--color-rose-800:oklch(.455 .188 13.697);--color-rose-900:oklch(.41 .159 10.272);--color-rose-950:oklch(.271 .105 12.094);--color-slate-50:oklch(.984 .003 247.858);--color-slate-100:oklch(.968 .007 247.896);--color-slate-200:oklch(.929 .013 255.508);--color-slate-300:oklch(.869 .022 252.894);--color-slate-400:oklch(.704 .04 256.788);--color-slate-500:oklch(.554 .046 257.417);--color-slate-600:oklch(.446 .043 257.281);--color-slate-700:oklch(.372 .044 257.287);--color-slate-800:oklch(.279 .041 260.031);--color-slate-900:oklch(.208 .042 265.755);--color-slate-950:oklch(.129 .042 264.695);--color-gray-50:oklch(.985 .002 247.839);--color-gray-100:oklch(.967 .003 264.542);--color-gray-200:oklch(.928 .006 264.531);--color-gray-300:oklch(.872 .01 258.338);--color-gray-400:oklch(.707 .022 261.325);--color-gray-500:oklch(.551 .027 264.364);--color-gray-600:oklch(.446 .03 256.802);--color-gray-700:oklch(.373 .034 259.733);--color-gray-800:oklch(.278 .033 256.848);--color-gray-900:oklch(.21 .034 264.665);--color-gray-950:oklch(.13 .028 261.692);--color-zinc-50:oklch(.985 0 0);--color-zinc-100:oklch(.967 .001 286.375);--color-zinc-200:oklch(.92 .004 286.32);--color-zinc-300:oklch(.871 .006 286.286);--color-zinc-400:oklch(.705 .015 286.067);--color-zinc-500:oklch(.552 .016 285.938);--color-zinc-600:oklch(.442 .017 285.786);--color-zinc-700:oklch(.37 .013 285.805);--color-zinc-800:oklch(.274 .006 286.033);--color-zinc-900:oklch(.21 .006 285.885);--color-zinc-950:oklch(.141 .005 285.823);--color-neutral-50:oklch(.985 0 0);--color-neutral-100:oklch(.97 0 0);--color-neutral-200:oklch(.922 0 0);--color-neutral-300:oklch(.87 0 0);--color-neutral-400:oklch(.708 0 0);--color-neutral-500:oklch(.556 0 0);--color-neutral-600:oklch(.439 0 0);--color-neutral-700:oklch(.371 0 0);--color-neutral-800:oklch(.269 0 0);--color-neutral-900:oklch(.205 0 0);--color-neutral-950:oklch(.145 0 0);--color-stone-50:oklch(.985 .001 106.423);--color-stone-100:oklch(.97 .001 106.424);--color-stone-200:oklch(.923 .003 48.717);--color-stone-300:oklch(.869 .005 56.366);--color-stone-400:oklch(.709 .01 56.259);--color-stone-500:oklch(.553 .013 58.071);--color-stone-600:oklch(.444 .011 73.639);--color-stone-700:oklch(.374 .01 67.558);--color-stone-800:oklch(.268 .007 34.298);--color-stone-900:oklch(.216 .006 56.043);--color-stone-950:oklch(.147 .004 49.25);--color-black:#000;--color-white:#fff;--spacing:.25rem;--breakpoint-sm:40rem;--breakpoint-md:48rem;--breakpoint-lg:64rem;--breakpoint-xl:80rem;--breakpoint-2xl:96rem;--container-3xs:16rem;--container-2xs:18rem;--container-xs:20rem;--container-sm:24rem;--container-md:28rem;--container-lg:32rem;--container-xl:36rem;--container-2xl:42rem;--container-3xl:48rem;--container-4xl:56rem;--container-5xl:64rem;--container-6xl:72rem;--container-7xl:80rem;--text-xs:.75rem;--text-xs--line-height:calc(1/.75);--text-sm:.875rem;--text-sm--line-height:calc(1.25/.875);--text-base:1rem;--text-base--line-height: 1.5 ;--text-lg:1.125rem;--text-lg--line-height:calc(1.75/1.125);--text-xl:1.25rem;--text-xl--line-height:calc(1.75/1.25);--text-2xl:1.5rem;--text-2xl--line-height:calc(2/1.5);--text-3xl:1.875rem;--text-3xl--line-height: 1.2 ;--text-4xl:2.25rem;--text-4xl--line-height:calc(2.5/2.25);--text-5xl:3rem;--text-5xl--line-height:1;--text-6xl:3.75rem;--text-6xl--line-height:1;--text-7xl:4.5rem;--text-7xl--line-height:1;--text-8xl:6rem;--text-8xl--line-height:1;--text-9xl:8rem;--text-9xl--line-height:1;--font-weight-thin:100;--font-weight-extralight:200;--font-weight-light:300;--font-weight-normal:400;--font-weight-medium:500;--font-weight-semibold:600;--font-weight-bold:700;--font-weight-extrabold:800;--font-weight-black:900;--tracking-tighter:-.05em;--tracking-tight:-.025em;--tracking-normal:0em;--tracking-wide:.025em;--tracking-wider:.05em;--tracking-widest:.1em;--leading-tight:1.25;--leading-snug:1.375;--leading-normal:1.5;--leading-relaxed:1.625;--leading-loose:2;--radius-xs:.125rem;--radius-sm:.25rem;--radius-md:.375rem;--radius-lg:.5rem;--radius-xl:.75rem;--radius-2xl:1rem;--radius-3xl:1.5rem;--radius-4xl:2rem;--shadow-2xs:0 1px #0000000d;--shadow-xs:0 1px 2px 0 #0000000d;--shadow-sm:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--shadow-md:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--shadow-lg:0 10px 15px -3px #0000001a,0 4px 6px -4px #0000001a;--shadow-xl:0 20px 25px -5px #0000001a,0 8px 10px -6px #0000001a;--shadow-2xl:0 25px 50px -12px #00000040;--inset-shadow-2xs:inset 0 1px #0000000d;--inset-shadow-xs:inset 0 1px 1px #0000000d;--inset-shadow-sm:inset 0 2px 4px #0000000d;--drop-shadow-xs:0 1px 1px #0000000d;--drop-shadow-sm:0 1px 2px #00000026;--drop-shadow-md:0 3px 3px #0000001f;--drop-shadow-lg:0 4px 4px #00000026;--drop-shadow-xl:0 9px 7px #0000001a;--drop-shadow-2xl:0 25px 25px #00000026;--ease-in:cubic-bezier(.4,0,1,1);--ease-out:cubic-bezier(0,0,.2,1);--ease-in-out:cubic-bezier(.4,0,.2,1);--animate-spin:spin 1s linear infinite;--animate-ping:ping 1s cubic-bezier(0,0,.2,1)infinite;--animate-pulse:pulse 2s cubic-bezier(.4,0,.6,1)infinite;--animate-bounce:bounce 1s infinite;--blur-xs:4px;--blur-sm:8px;--blur-md:12px;--blur-lg:16px;--blur-xl:24px;--blur-2xl:40px;--blur-3xl:64px;--perspective-dramatic:100px;--perspective-near:300px;--perspective-normal:500px;--perspective-midrange:800px;--perspective-distant:1200px;--aspect-video:16/9;--default-transition-duration:.15s;--default-transition-timing-function:cubic-bezier(.4,0,.2,1);--default-font-family:var(--font-sans);--default-font-feature-settings:var(--font-sans--font-feature-settings);--default-font-variation-settings:var(--font-sans--font-variation-settings);--default-mono-font-family:var(--font-mono);--default-mono-font-feature-settings:var(--font-mono--font-feature-settings);--default-mono-font-variation-settings:var(--font-mono--font-variation-settings)}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}::file-selector-button{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;-moz-tab-size:4;tab-size:4;line-height:1.5;font-family:var(--default-font-family,ui-sans-serif,system-ui,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji");font-feature-settings:var(--default-font-feature-settings,normal);font-variation-settings:var(--default-font-variation-settings,normal);-webkit-tap-highlight-color:transparent}body{line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;-webkit-text-decoration:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,samp,pre{font-family:var(--default-mono-font-family,ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono","Courier New",monospace);font-feature-settings:var(--default-mono-font-feature-settings,normal);font-variation-settings:var(--default-mono-font-variation-settings,normal);font-size:1em}small{font-size:80%}sub,sup{vertical-align:baseline;font-size:75%;line-height:0;position:relative}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}:-moz-focusring{outline:auto}progress{vertical-align:baseline}summary{display:list-item}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{vertical-align:middle;display:block}img,video{max-width:100%;height:auto}button,input,select,optgroup,textarea{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}::file-selector-button{font:inherit;font-feature-settings:inherit;font-variation-settings:inherit;letter-spacing:inherit;color:inherit;opacity:1;background-color:#0000;border-radius:0}:where(select:is([multiple],[size])) optgroup{font-weight:bolder}:where(select:is([multiple],[size])) optgroup option{padding-inline-start:20px}::file-selector-button{margin-inline-end:4px}::placeholder{opacity:1;color:color-mix(in oklab,currentColor 50%,transparent)}textarea{resize:vertical}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-date-and-time-value{min-height:1lh;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-datetime-edit{padding-block:0}::-webkit-datetime-edit-year-field{padding-block:0}::-webkit-datetime-edit-month-field{padding-block:0}::-webkit-datetime-edit-day-field{padding-block:0}::-webkit-datetime-edit-hour-field{padding-block:0}::-webkit-datetime-edit-minute-field{padding-block:0}::-webkit-datetime-edit-second-field{padding-block:0}::-webkit-datetime-edit-millisecond-field{padding-block:0}::-webkit-datetime-edit-meridiem-field{padding-block:0}:-moz-ui-invalid{box-shadow:none}button,input:where([type=button],[type=reset],[type=submit]){-webkit-appearance:button;-moz-appearance:button;appearance:button}::file-selector-button{-webkit-appearance:button;-moz-appearance:button;appearance:button}::-webkit-inner-spin-button{height:auto}::-webkit-outer-spin-button{height:auto}[hidden]:where(:not([hidden=until-found])){display:none!important}}@layer components;@layer utilities{.absolute{position:absolute}.relative{position:relative}.static{position:static}.inset-0{inset:calc(var(--spacing)*0)}.-mt-\[4\.9rem\]{margin-top:-4.9rem}.-mb-px{margin-bottom:-1px}.mb-1{margin-bottom:calc(var(--spacing)*1)}.mb-2{margin-bottom:calc(var(--spacing)*2)}.mb-4{margin-bottom:calc(var(--spacing)*4)}.mb-6{margin-bottom:calc(var(--spacing)*6)}.-ml-8{margin-left:calc(var(--spacing)*-8)}.flex{display:flex}.hidden{display:none}.inline-block{display:inline-block}.inline-flex{display:inline-flex}.table{display:table}.aspect-\[335\/376\]{aspect-ratio:335/376}.h-1{height:calc(var(--spacing)*1)}.h-1\.5{height:calc(var(--spacing)*1.5)}.h-2{height:calc(var(--spacing)*2)}.h-2\.5{height:calc(var(--spacing)*2.5)}.h-3{height:calc(var(--spacing)*3)}.h-3\.5{height:calc(var(--spacing)*3.5)}.h-14{height:calc(var(--spacing)*14)}.h-14\.5{height:calc(var(--spacing)*14.5)}.min-h-screen{min-height:100vh}.w-1{width:calc(var(--spacing)*1)}.w-1\.5{width:calc(var(--spacing)*1.5)}.w-2{width:calc(var(--spacing)*2)}.w-2\.5{width:calc(var(--spacing)*2.5)}.w-3{width:calc(var(--spacing)*3)}.w-3\.5{width:calc(var(--spacing)*3.5)}.w-\[448px\]{width:448px}.w-full{width:100%}.max-w-\[335px\]{max-width:335px}.max-w-none{max-width:none}.flex-1{flex:1}.shrink-0{flex-shrink:0}.translate-y-0{--tw-translate-y:calc(var(--spacing)*0);translate:var(--tw-translate-x)var(--tw-translate-y)}.transform{transform:var(--tw-rotate-x)var(--tw-rotate-y)var(--tw-rotate-z)var(--tw-skew-x)var(--tw-skew-y)}.flex-col{flex-direction:column}.flex-col-reverse{flex-direction:column-reverse}.items-center{align-items:center}.justify-center{justify-content:center}.justify-end{justify-content:flex-end}.gap-3{gap:calc(var(--spacing)*3)}.gap-4{gap:calc(var(--spacing)*4)}:where(.space-x-1>:not(:last-child)){--tw-space-x-reverse:0;margin-inline-start:calc(calc(var(--spacing)*1)*var(--tw-space-x-reverse));margin-inline-end:calc(calc(var(--spacing)*1)*calc(1 - var(--tw-space-x-reverse)))}.overflow-hidden{overflow:hidden}.rounded-full{border-radius:3.40282e38px}.rounded-sm{border-radius:var(--radius-sm)}.rounded-t-lg{border-top-left-radius:var(--radius-lg);border-top-right-radius:var(--radius-lg)}.rounded-br-lg{border-bottom-right-radius:var(--radius-lg)}.rounded-bl-lg{border-bottom-left-radius:var(--radius-lg)}.border{border-style:var(--tw-border-style);border-width:1px}.border-\[\#19140035\]{border-color:#19140035}.border-\[\#e3e3e0\]{border-color:#e3e3e0}.border-black{border-color:var(--color-black)}.border-transparent{border-color:#0000}.bg-\[\#1b1b18\]{background-color:#1b1b18}.bg-\[\#FDFDFC\]{background-color:#fdfdfc}.bg-\[\#dbdbd7\]{background-color:#dbdbd7}.bg-\[\#fff2f2\]{background-color:#fff2f2}.bg-white{background-color:var(--color-white)}.p-6{padding:calc(var(--spacing)*6)}.px-5{padding-inline:calc(var(--spacing)*5)}.py-1{padding-block:calc(var(--spacing)*1)}.py-1\.5{padding-block:calc(var(--spacing)*1.5)}.py-2{padding-block:calc(var(--spacing)*2)}.pb-12{padding-bottom:calc(var(--spacing)*12)}.text-sm{font-size:var(--text-sm);line-height:var(--tw-leading,var(--text-sm--line-height))}.text-\[13px\]{font-size:13px}.leading-\[20px\]{--tw-leading:20px;line-height:20px}.leading-normal{--tw-leading:var(--leading-normal);line-height:var(--leading-normal)}.font-medium{--tw-font-weight:var(--font-weight-medium);font-weight:var(--font-weight-medium)}.text-\[\#1b1b18\]{color:#1b1b18}.text-\[\#706f6c\]{color:#706f6c}.text-\[\#F53003\],.text-\[\#f53003\]{color:#f53003}.text-white{color:var(--color-white)}.underline{text-decoration-line:underline}.underline-offset-4{text-underline-offset:4px}.opacity-100{opacity:1}.shadow-\[0px_0px_1px_0px_rgba\(0\,0\,0\,0\.03\)\,0px_1px_2px_0px_rgba\(0\,0\,0\,0\.06\)\]{--tw-shadow:0px 0px 1px 0px var(--tw-shadow-color,#00000008),0px 1px 2px 0px var(--tw-shadow-color,#0000000f);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.shadow-\[inset_0px_0px_0px_1px_rgba\(26\,26\,0\,0\.16\)\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#1a1a0029);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.\!filter{filter:var(--tw-blur,)var(--tw-brightness,)var(--tw-contrast,)var(--tw-grayscale,)var(--tw-hue-rotate,)var(--tw-invert,)var(--tw-saturate,)var(--tw-sepia,)var(--tw-drop-shadow,)!important}.filter{filter:var(--tw-blur,)var(--tw-brightness,)var(--tw-contrast,)var(--tw-grayscale,)var(--tw-hue-rotate,)var(--tw-invert,)var(--tw-saturate,)var(--tw-sepia,)var(--tw-drop-shadow,)}.transition-all{transition-property:all;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.transition-opacity{transition-property:opacity;transition-timing-function:var(--tw-ease,var(--default-transition-timing-function));transition-duration:var(--tw-duration,var(--default-transition-duration))}.delay-300{transition-delay:.3s}.duration-750{--tw-duration:.75s;transition-duration:.75s}.not-has-\[nav\]\:hidden:not(:has(:is(nav))){display:none}.before\:absolute:before{content:var(--tw-content);position:absolute}.before\:top-0:before{content:var(--tw-content);top:calc(var(--spacing)*0)}.before\:top-1\/2:before{content:var(--tw-content);top:50%}.before\:bottom-0:before{content:var(--tw-content);bottom:calc(var(--spacing)*0)}.before\:bottom-1\/2:before{content:var(--tw-content);bottom:50%}.before\:left-\[0\.4rem\]:before{content:var(--tw-content);left:.4rem}.before\:border-l:before{content:var(--tw-content);border-left-style:var(--tw-border-style);border-left-width:1px}.before\:border-\[\#e3e3e0\]:before{content:var(--tw-content);border-color:#e3e3e0}@media (hover:hover){.hover\:border-\[\#1915014a\]:hover{border-color:#1915014a}.hover\:border-\[\#19140035\]:hover{border-color:#19140035}.hover\:border-black:hover{border-color:var(--color-black)}.hover\:bg-black:hover{background-color:var(--color-black)}}@media (width>=64rem){.lg\:-mt-\[6\.6rem\]{margin-top:-6.6rem}.lg\:mb-0{margin-bottom:calc(var(--spacing)*0)}.lg\:mb-6{margin-bottom:calc(var(--spacing)*6)}.lg\:-ml-px{margin-left:-1px}.lg\:ml-0{margin-left:calc(var(--spacing)*0)}.lg\:block{display:block}.lg\:aspect-auto{aspect-ratio:auto}.lg\:w-\[438px\]{width:438px}.lg\:max-w-4xl{max-width:var(--container-4xl)}.lg\:grow{flex-grow:1}.lg\:flex-row{flex-direction:row}.lg\:justify-center{justify-content:center}.lg\:rounded-t-none{border-top-left-radius:0;border-top-right-radius:0}.lg\:rounded-tl-lg{border-top-left-radius:var(--radius-lg)}.lg\:rounded-r-lg{border-top-right-radius:var(--radius-lg);border-bottom-right-radius:var(--radius-lg)}.lg\:rounded-br-none{border-bottom-right-radius:0}.lg\:p-8{padding:calc(var(--spacing)*8)}.lg\:p-20{padding:calc(var(--spacing)*20)}}@media (prefers-color-scheme:dark){.dark\:block{display:block}.dark\:hidden{display:none}.dark\:border-\[\#3E3E3A\]{border-color:#3e3e3a}.dark\:border-\[\#eeeeec\]{border-color:#eeeeec}.dark\:bg-\[\#0a0a0a\]{background-color:#0a0a0a}.dark\:bg-\[\#1D0002\]{background-color:#1d0002}.dark\:bg-\[\#3E3E3A\]{background-color:#3e3e3a}.dark\:bg-\[\#161615\]{background-color:#161615}.dark\:bg-\[\#eeeeec\]{background-color:#eeeeec}.dark\:text-\[\#1C1C1A\]{color:#1c1c1a}.dark\:text-\[\#A1A09A\]{color:#a1a09a}.dark\:text-\[\#EDEDEC\]{color:#ededec}.dark\:text-\[\#F61500\]{color:#f61500}.dark\:text-\[\#FF4433\]{color:#f43}.dark\:shadow-\[inset_0px_0px_0px_1px_\#fffaed2d\]{--tw-shadow:inset 0px 0px 0px 1px var(--tw-shadow-color,#fffaed2d);box-shadow:var(--tw-inset-shadow),var(--tw-inset-ring-shadow),var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}.dark\:before\:border-\[\#3E3E3A\]:before{content:var(--tw-content);border-color:#3e3e3a}@media (hover:hover){.dark\:hover\:border-\[\#3E3E3A\]:hover{border-color:#3e3e3a}.dark\:hover\:border-\[\#62605b\]:hover{border-color:#62605b}.dark\:hover\:border-white:hover{border-color:var(--color-white)}.dark\:hover\:bg-white:hover{background-color:var(--color-white)}}}@starting-style{.starting\:translate-y-4{--tw-translate-y:calc(var(--spacing)*4);translate:var(--tw-translate-x)var(--tw-translate-y)}}@starting-style{.starting\:translate-y-6{--tw-translate-y:calc(var(--spacing)*6);translate:var(--tw-translate-x)var(--tw-translate-y)}}@starting-style{.starting\:opacity-0{opacity:0}}}@keyframes spin{to{transform:rotate(360deg)}}@keyframes ping{75%,to{opacity:0;transform:scale(2)}}@keyframes pulse{50%{opacity:.5}}@keyframes bounce{0%,to{animation-timing-function:cubic-bezier(.8,0,1,1);transform:translateY(-25%)}50%{animation-timing-function:cubic-bezier(0,0,.2,1);transform:none}}@property --tw-translate-x{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-y{syntax:"*";inherits:false;initial-value:0}@property --tw-translate-z{syntax:"*";inherits:false;initial-value:0}@property --tw-rotate-x{syntax:"*";inherits:false;initial-value:rotateX(0)}@property --tw-rotate-y{syntax:"*";inherits:false;initial-value:rotateY(0)}@property --tw-rotate-z{syntax:"*";inherits:false;initial-value:rotateZ(0)}@property --tw-skew-x{syntax:"*";inherits:false;initial-value:skewX(0)}@property --tw-skew-y{syntax:"*";inherits:false;initial-value:skewY(0)}@property --tw-space-x-reverse{syntax:"*";inherits:false;initial-value:0}@property --tw-border-style{syntax:"*";inherits:false;initial-value:solid}@property --tw-leading{syntax:"*";inherits:false}@property --tw-font-weight{syntax:"*";inherits:false}@property --tw-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-shadow-color{syntax:"*";inherits:false}@property --tw-inset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-shadow-color{syntax:"*";inherits:false}@property --tw-ring-color{syntax:"*";inherits:false}@property --tw-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-inset-ring-color{syntax:"*";inherits:false}@property --tw-inset-ring-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-ring-inset{syntax:"*";inherits:false}@property --tw-ring-offset-width{syntax:"<length>";inherits:false;initial-value:0}@property --tw-ring-offset-color{syntax:"*";inherits:false;initial-value:#fff}@property --tw-ring-offset-shadow{syntax:"*";inherits:false;initial-value:0 0 #0000}@property --tw-blur{syntax:"*";inherits:false}@property --tw-brightness{syntax:"*";inherits:false}@property --tw-contrast{syntax:"*";inherits:false}@property --tw-grayscale{syntax:"*";inherits:false}@property --tw-hue-rotate{syntax:"*";inherits:false}@property --tw-invert{syntax:"*";inherits:false}@property --tw-opacity{syntax:"*";inherits:false}@property --tw-saturate{syntax:"*";inherits:false}@property --tw-sepia{syntax:"*";inherits:false}@property --tw-drop-shadow{syntax:"*";inherits:false}@property --tw-duration{syntax:"*";inherits:false}@property --tw-content{syntax:"*";inherits:false;initial-value:""}
        </style>
    @endif
    <!-- ==== WOW JS ==== -->
    <script src="storage/js/wow.min.js"></script>
    <script>
        new WOW().init();
    </script>
</head>

<body>
<!-- ====== Navbar Section Start -->
<div
    class="absolute top-0 left-0 z-40 flex items-center w-full bg-transparent ud-header"
>
    <div class="container px-4 mx-auto">
        <div class="relative flex items-center justify-between -mx-4">
            <div class="max-w-full px-4 w-60">
                <a href="/" class="block w-full py-5 navbar-logo">
                    <img
                        src="storage/images/logo/logo-white.png"
                        alt="logo"
                        class="w-full header-logo"
                    />
                </a>
            </div>
            <div class="flex items-center justify-between w-full px-4">
                <div>
                    <button
                        id="navbarToggler"
                        class="absolute right-4 top-1/2 block -translate-y-1/2 rounded-lg px-3 py-[6px] ring-primary focus:ring-2 lg:hidden"
                    >
                <span
                    class="relative my-[6px] block h-[2px] w-[30px] bg-white"
                ></span>
                        <span
                            class="relative my-[6px] block h-[2px] w-[30px] bg-white"
                        ></span>
                        <span
                            class="relative my-[6px] block h-[2px] w-[30px] bg-white"
                        ></span>
                    </button>
                </div>
                <div class="flex items-center justify-end pr-16 lg:pr-0">
                    <div class="hidden sm:flex">
                        <a
                            href="{{ route('coach.dashboard') }}"
                            class="loginBtn px-[22px] py-2 text-base font-medium text-white hover:opacity-70"
                        >
                            Sign In
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ====== Navbar Section End -->

<!-- ====== Hero Section Start -->
<div
    id="home"
    class="relative overflow-hidden bg-primary pt-[120px] md:pt-[130px] lg:pt-[160px]"
>
    <div class="container px-4 mx-auto">
        <div class="flex flex-wrap items-center -mx-4">
            <div class="w-full px-4">
                <div
                    class="hero-content wow fadeInUp mx-auto max-w-[780px] text-center"
                    data-wow-delay=".2s"
                >
                    <h1
                        class="mb-6 text-3xl font-bold leading-snug text-white sm:text-4xl sm:leading-snug lg:text-5xl lg:leading-[1.2]"
                    >
                        LiftDeck
                    </h1>
                    <p
                        class="mx-auto mb-9 max-w-[600px] text-base font-medium text-white sm:text-lg sm:leading-[1.44]"
                    >
                        Elevate your coaching experience to next level
                    </p>
                    <ul
                        class="flex flex-wrap items-center justify-center gap-5 mb-10"
                    >
                        <li>
                            <a
                                href="mailto:beta@liftdeck.io"
                                class="inline-flex items-center justify-center rounded-md bg-white px-7 py-[14px] text-center text-base font-medium text-dark shadow-1 transition duration-300 ease-in-out hover:bg-gray-2 hover:text-body-color"
                            >
                                Contact us
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="w-full px-4">
                <div
                    class="wow fadeInUp relative z-10 mx-auto max-w-[845px]"
                    data-wow-delay=".25s"
                >
                    <div class="mt-16">
                        <img
                            src="storage/images/hero/hero-image.png"
                            alt="hero"
                            class="max-w-full mx-auto rounded-t-xl rounded-tr-xl"
                        />
                    </div>
                    <div class="absolute -left-9 bottom-0 z-[-1]">
                        <svg
                            width="134"
                            height="106"
                            viewBox="0 0 134 106"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="1.66667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 1.66667 104)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 16.3333 104)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 31 104)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 45.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 60.3333 104)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 88.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 117.667 104)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 74.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 103 104)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 132 104)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 1.66667 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 16.3333 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 31 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 45.6667 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 60.3333 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 88.6667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 117.667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 74.6667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 103 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 132 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="74.6673"
                                r="1.66667"
                                transform="rotate(-90 1.66667 74.6673)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 1.66667 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 16.3333 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 16.3333 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 31 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 31 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 45.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 45.6667 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 60.3333 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 60.3333 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 88.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 88.6667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 117.667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 117.667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 74.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 74.6667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 103 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 103 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 132 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 132 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 1.66667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 1.66667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 16.3333 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 16.3333 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 31 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 31 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 45.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 45.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 60.3333 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 60.3333 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 88.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 88.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 117.667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 117.667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 74.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 74.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 103 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 103 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 132 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 132 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 1.66667 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 1.66667 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 16.3333 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 16.3333 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 31 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 31 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 45.6667 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 45.6667 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 60.3333 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 60.3333 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 88.6667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 88.6667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 117.667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 117.667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 74.6667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 74.6667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 103 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 103 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 132 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 132 1.66707)"
                                fill="white"
                            />
                        </svg>
                    </div>
                    <div class="absolute -right-6 -top-6 z-[-1]">
                        <svg
                            width="134"
                            height="106"
                            viewBox="0 0 134 106"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                cx="1.66667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 1.66667 104)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 16.3333 104)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 31 104)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 45.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 60.3333 104)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 88.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 117.667 104)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 74.6667 104)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 103 104)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="104"
                                r="1.66667"
                                transform="rotate(-90 132 104)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 1.66667 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 16.3333 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 31 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="89.3333"
                                r="1.66667"
                                transform="rotate(-90 45.6667 89.3333)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 60.3333 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 88.6667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 117.667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 74.6667 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 103 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="89.3338"
                                r="1.66667"
                                transform="rotate(-90 132 89.3338)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="74.6673"
                                r="1.66667"
                                transform="rotate(-90 1.66667 74.6673)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 1.66667 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 16.3333 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 16.3333 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 31 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 31 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 45.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="31.0003"
                                r="1.66667"
                                transform="rotate(-90 45.6667 31.0003)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 60.3333 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 60.3333 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 88.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 88.6667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 117.667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 117.667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 74.6667 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 74.6667 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 103 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 103 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="74.6668"
                                r="1.66667"
                                transform="rotate(-90 132 74.6668)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="31.0001"
                                r="1.66667"
                                transform="rotate(-90 132 31.0001)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 1.66667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 1.66667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 16.3333 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 16.3333 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 31 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 31 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 45.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 45.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 60.3333 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 60.3333 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 88.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 88.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 117.667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 117.667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 74.6667 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 74.6667 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 103 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 103 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="60.0003"
                                r="1.66667"
                                transform="rotate(-90 132 60.0003)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="16.3336"
                                r="1.66667"
                                transform="rotate(-90 132 16.3336)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 1.66667 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="1.66667"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 1.66667 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 16.3333 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="16.3333"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 16.3333 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 31 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="31"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 31 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="45.3336"
                                r="1.66667"
                                transform="rotate(-90 45.6667 45.3336)"
                                fill="white"
                            />
                            <circle
                                cx="45.6667"
                                cy="1.66683"
                                r="1.66667"
                                transform="rotate(-90 45.6667 1.66683)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 60.3333 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="60.3333"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 60.3333 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 88.6667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="88.6667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 88.6667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 117.667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="117.667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 117.667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 74.6667 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="74.6667"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 74.6667 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 103 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="103"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 103 1.66707)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="45.3338"
                                r="1.66667"
                                transform="rotate(-90 132 45.3338)"
                                fill="white"
                            />
                            <circle
                                cx="132"
                                cy="1.66707"
                                r="1.66667"
                                transform="rotate(-90 132 1.66707)"
                                fill="white"
                            />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ====== Hero Section End -->

<!-- ====== Features Section Start -->
<section class="pb-8 pt-20 dark:bg-dark lg:pb-[70px] lg:pt-[120px]">
    <div class="container px-4 mx-auto">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4">
                <div class="mx-auto mb-12 max-w-[485px] text-center lg:mb-[70px]">
              <span class="block mb-2 text-lg font-semibold text-primary">
                Features
              </span>
                    <h2
                        class="mb-3 text-3xl font-bold text-dark dark:text-white sm:text-4xl md:text-[40px] md:leading-[1.2]"
                    >
                        Main Features Of LiftDeck
                    </h2>
                    <p class="text-base text-body-color dark:text-dark-6">
                        Onboard clients in minutes with guided intake forms that capture goals, health history, and equipment. Coaches get instant "client-ready" profiles with auto-flagged risks and constraints.
                    </p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                <div class="mb-12 wow fadeInUp group" data-wow-delay=".1s">
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary"
                    >
                <span
                    class="absolute left-0 top-0 -z-1 mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary/20 duration-300 group-hover:rotate-45"
                ></span>
                        <svg
                            width="37"
                            height="37"
                            viewBox="0 0 37 37"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M30.5801 8.30514H27.9926C28.6113 7.85514 29.1176 7.34889 29.3426 6.73014C29.6801 5.88639 29.6801 4.48014 27.9363 2.84889C26.0801 1.04889 24.3926 1.04889 23.3238 1.33014C20.9051 1.94889 19.2738 4.76139 18.3738 6.78639C17.4738 4.76139 15.8426 2.00514 13.4238 1.33014C12.3551 1.04889 10.6676 1.10514 8.81133 2.84889C7.06758 4.53639 7.12383 5.88639 7.40508 6.73014C7.63008 7.34889 8.13633 7.85514 8.75508 8.30514H5.71758C4.08633 8.30514 2.73633 9.65514 2.73633 11.2864V14.9989C2.73633 16.5739 4.03008 17.8676 5.60508 17.9239V31.6489C5.60508 33.5614 7.18008 35.1926 9.14883 35.1926H27.5426C29.4551 35.1926 31.0863 33.6176 31.0863 31.6489V17.8676C32.4926 17.6426 33.5613 16.4051 33.5613 14.9426V11.2301C33.5613 9.59889 32.2113 8.30514 30.5801 8.30514ZM23.9426 3.69264C23.9988 3.69264 24.1676 3.63639 24.3363 3.63639C24.7301 3.63639 25.3488 3.80514 26.1926 4.59264C26.8676 5.21139 27.0363 5.66139 26.9801 5.77389C26.6988 6.56139 23.8863 7.40514 20.6801 7.74264C21.4676 5.99889 22.6488 4.03014 23.9426 3.69264ZM10.4988 4.64889C11.3426 3.86139 11.9613 3.69264 12.3551 3.69264C12.5238 3.69264 12.6363 3.74889 12.7488 3.74889C14.0426 4.08639 15.2801 5.99889 16.0676 7.79889C12.8613 7.46139 10.0488 6.61764 9.76758 5.83014C9.71133 5.66139 9.88008 5.26764 10.4988 4.64889ZM5.26758 14.9426V11.2301C5.26758 11.0051 5.43633 10.7801 5.71758 10.7801H30.5801C30.8051 10.7801 31.0301 10.9489 31.0301 11.2301V14.9426C31.0301 15.1676 30.8613 15.3926 30.5801 15.3926H5.71758C5.49258 15.3926 5.26758 15.2239 5.26758 14.9426ZM27.5426 32.6614H9.14883C8.58633 32.6614 8.13633 32.2114 8.13633 31.6489V17.9239H28.4988V31.6489C28.5551 32.2114 28.1051 32.6614 27.5426 32.6614Z"
                                fill="white"
                            />
                        </svg>
                    </div>
                    <h4 class="mb-3 text-xl font-bold text-dark dark:text-white">
                        Smart Client Onboarding
                    </h4>
                    <p class="mb-8 text-body-color dark:text-dark-6 lg:mb-9">
                        Onboard clients in minutes with guided intake forms that capture goals, health history, and equipment. Coaches get instant "client-ready" profiles with auto-flagged risks and constraints.                    </p>

                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                <div class="mb-12 wow fadeInUp group" data-wow-delay=".15s">
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary"
                    >
                <span
                    class="absolute left-0 top-0 -z-1 mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary/20 duration-300 group-hover:rotate-45"
                ></span>
                        <svg
                            width="36"
                            height="36"
                            viewBox="0 0 36 36"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M30.5998 1.01245H5.39981C2.98105 1.01245 0.956055 2.9812 0.956055 5.4562V30.6562C0.956055 33.075 2.9248 35.0437 5.39981 35.0437H30.5998C33.0186 35.0437 34.9873 33.075 34.9873 30.6562V5.39995C34.9873 2.9812 33.0186 1.01245 30.5998 1.01245ZM5.39981 3.48745H30.5998C31.6123 3.48745 32.4561 4.3312 32.4561 5.39995V11.1937H3.4873V5.39995C3.4873 4.38745 4.38731 3.48745 5.39981 3.48745ZM3.4873 30.6V13.725H23.0623V32.5125H5.39981C4.38731 32.5125 3.4873 31.6125 3.4873 30.6ZM30.5998 32.5125H25.5373V13.725H32.4561V30.6C32.5123 31.6125 31.6123 32.5125 30.5998 32.5125Z"
                                fill="white"
                            />
                        </svg>
                    </div>
                    <h4 class="mb-3 text-xl font-bold text-dark dark:text-white">
                        Drag & Drop Training
                    </h4>
                    <p class="mb-8 text-body-color dark:text-dark-6 lg:mb-9">
                        Build and assign personalized training programs using drag-and-drop workouts and 100+ exercise templates. Auto-progression rules keep clients advancing without manual tweaks.                    </p>

                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                <div class="mb-12 wow fadeInUp group" data-wow-delay=".2s">
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary"
                    >
                <span
                    class="absolute left-0 top-0 -z-1 mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary/20 duration-300 group-hover:rotate-45"
                ></span>
                        <svg
                            width="37"
                            height="37"
                            viewBox="0 0 37 37"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M33.5613 21.4677L31.3675 20.1177C30.805 19.7239 30.0175 19.9489 29.6238 20.5114C29.23 21.1302 29.455 21.8614 30.0175 22.2552L31.48 23.2114L18.1488 31.5927L4.76127 23.2114L6.22377 22.2552C6.84252 21.8614 7.01127 21.0739 6.61752 20.5114C6.22377 19.8927 5.43627 19.7239 4.87377 20.1177L2.68002 21.4677C2.11752 21.8614 1.72377 22.4802 1.72377 23.1552C1.72377 23.8302 2.06127 24.5052 2.68002 24.8427L17.08 33.8989C17.4175 34.1239 17.755 34.1802 18.1488 34.1802C18.5425 34.1802 18.88 34.0677 19.2175 33.8989L33.5613 24.8989C34.1238 24.5052 34.5175 23.8864 34.5175 23.2114C34.5175 22.5364 34.18 21.8614 33.5613 21.4677Z"
                                fill="white"
                            />
                            <path
                                d="M20.1175 20.4552L18.1488 21.6364L16.18 20.3989C15.5613 20.0052 14.83 20.2302 14.4363 20.7927C14.0425 21.4114 14.2675 22.1427 14.83 22.5364L17.4738 24.1677C17.6988 24.2802 17.9238 24.3364 18.1488 24.3364C18.3738 24.3364 18.5988 24.2802 18.8238 24.1677L21.4675 22.5364C22.0863 22.1427 22.255 21.3552 21.8613 20.7927C21.4675 20.2302 20.68 20.0614 20.1175 20.4552Z"
                                fill="white"
                            />
                            <path
                                d="M7.74252 18.0927L11.455 20.4552C11.68 20.5677 11.905 20.6239 12.13 20.6239C12.5238 20.6239 12.9738 20.3989 13.1988 20.0052C13.5925 19.3864 13.3675 18.6552 12.805 18.2614L9.09252 15.8989C8.47377 15.5052 7.74252 15.7302 7.34877 16.2927C6.95502 16.9677 7.12377 17.7552 7.74252 18.0927Z"
                                fill="white"
                            />
                            <path
                                d="M5.04252 16.1802C5.43627 16.1802 5.88627 15.9552 6.11127 15.5614C6.50502 14.9427 6.28002 14.2114 5.71752 13.8177L4.81752 13.2552L5.71752 12.6927C6.33627 12.2989 6.50502 11.5114 6.11127 10.9489C5.71752 10.3302 4.93002 10.1614 4.36752 10.5552L1.72377 12.1864C1.33002 12.4114 1.10502 12.8052 1.10502 13.2552C1.10502 13.7052 1.33002 14.0989 1.72377 14.3239L4.36752 15.9552C4.53627 16.1239 4.76127 16.1802 5.04252 16.1802Z"
                                fill="white"
                            />
                            <path
                                d="M8.41752 10.7239C8.64252 10.7239 8.86752 10.6677 9.09252 10.5552L12.805 8.1927C13.4238 7.79895 13.5925 7.01145 13.1988 6.44895C12.805 5.8302 12.0175 5.66145 11.455 6.0552L7.74252 8.4177C7.12377 8.81145 6.95502 9.59895 7.34877 10.1614C7.57377 10.4989 7.96752 10.7239 8.41752 10.7239Z"
                                fill="white"
                            />
                            <path
                                d="M16.18 6.05522L18.1488 4.81772L20.1175 6.05522C20.3425 6.16772 20.5675 6.22397 20.7925 6.22397C21.1863 6.22397 21.6363 5.99897 21.8613 5.60522C22.255 4.98647 22.03 4.25522 21.4675 3.86147L18.8238 2.23022C18.43 1.94897 17.8675 1.94897 17.4738 2.23022L14.83 3.86147C14.2113 4.25522 14.0425 5.04272 14.4363 5.60522C14.83 6.16772 15.6175 6.44897 16.18 6.05522Z"
                                fill="white"
                            />
                            <path
                                d="M23.4925 8.19267L27.205 10.5552C27.43 10.6677 27.655 10.7239 27.88 10.7239C28.2738 10.7239 28.7238 10.4989 28.9488 10.1052C29.3425 9.48642 29.1175 8.75517 28.555 8.36142L24.8425 5.99892C24.28 5.60517 23.4925 5.83017 23.0988 6.39267C22.705 7.01142 22.8738 7.79892 23.4925 8.19267Z"
                                fill="white"
                            />
                            <path
                                d="M34.5738 12.1864L31.93 10.5552C31.3675 10.1614 30.58 10.3864 30.1863 10.9489C29.7925 11.5677 30.0175 12.2989 30.58 12.6927L31.48 13.2552L30.58 13.8177C29.9613 14.2114 29.7925 14.9989 30.1863 15.5614C30.4113 15.9552 30.8613 16.1802 31.255 16.1802C31.48 16.1802 31.705 16.1239 31.93 16.0114L34.5738 14.3802C34.9675 14.1552 35.1925 13.7614 35.1925 13.3114C35.1925 12.8614 34.9675 12.4114 34.5738 12.1864Z"
                                fill="white"
                            />
                            <path
                                d="M24.1675 20.624C24.3925 20.624 24.6175 20.5677 24.8425 20.4552L28.555 18.0927C29.1738 17.699 29.3425 16.9115 28.9488 16.349C28.555 15.7302 27.7675 15.5615 27.205 15.9552L23.4925 18.3177C22.8738 18.7115 22.705 19.499 23.0988 20.0615C23.3238 20.4552 23.7175 20.624 24.1675 20.624Z"
                                fill="white"
                            />
                        </svg>
                    </div>
                    <h4 class="mb-3 text-xl font-bold text-dark dark:text-white">
                        Custom Meal Planning
                    </h4>
                    <p class="mb-8 text-body-color dark:text-dark-6 lg:mb-9">
                        Create custom meal plans with macro calculators and reusable templates. Clients log meals via photos for easy adherence tracking and weekly nutrition scores.                    </p>

                </div>
            </div>
            <div class="w-full px-4 md:w-1/2 lg:w-1/4">
                <div class="mb-12 wow fadeInUp group" data-wow-delay=".25s">
                    <div
                        class="relative z-10 mb-10 flex h-[70px] w-[70px] items-center justify-center rounded-[14px] bg-primary"
                    >
                <span
                    class="absolute left-0 top-0 -z-1 mb-8 flex h-[70px] w-[70px] rotate-[25deg] items-center justify-center rounded-[14px] bg-primary/20 duration-300 group-hover:rotate-45"
                ></span>
                        <svg
                            width="37"
                            height="37"
                            viewBox="0 0 37 37"
                            fill="none"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M12.355 2.0614H5.21129C3.29879 2.0614 1.72379 3.6364 1.72379 5.5489V12.6927C1.72379 14.6052 3.29879 16.1802 5.21129 16.1802H12.355C14.2675 16.1802 15.8425 14.6052 15.8425 12.6927V5.60515C15.8988 3.6364 14.3238 2.0614 12.355 2.0614ZM13.3675 12.7489C13.3675 13.3114 12.9175 13.7614 12.355 13.7614H5.21129C4.64879 13.7614 4.19879 13.3114 4.19879 12.7489V5.60515C4.19879 5.04265 4.64879 4.59265 5.21129 4.59265H12.355C12.9175 4.59265 13.3675 5.04265 13.3675 5.60515V12.7489Z"
                                fill="white"
                            />
                            <path
                                d="M31.0863 2.0614H23.9425C22.03 2.0614 20.455 3.6364 20.455 5.5489V12.6927C20.455 14.6052 22.03 16.1802 23.9425 16.1802H31.0863C32.9988 16.1802 34.5738 14.6052 34.5738 12.6927V5.60515C34.5738 3.6364 32.9988 2.0614 31.0863 2.0614ZM32.0988 12.7489C32.0988 13.3114 31.6488 13.7614 31.0863 13.7614H23.9425C23.38 13.7614 22.93 13.3114 22.93 12.7489V5.60515C22.93 5.04265 23.38 4.59265 23.9425 4.59265H31.0863C31.6488 4.59265 32.0988 5.04265 32.0988 5.60515V12.7489Z"
                                fill="white"
                            />
                            <path
                                d="M12.355 20.0051H5.21129C3.29879 20.0051 1.72379 21.5801 1.72379 23.4926V30.6364C1.72379 32.5489 3.29879 34.1239 5.21129 34.1239H12.355C14.2675 34.1239 15.8425 32.5489 15.8425 30.6364V23.5489C15.8988 21.5801 14.3238 20.0051 12.355 20.0051ZM13.3675 30.6926C13.3675 31.2551 12.9175 31.7051 12.355 31.7051H5.21129C4.64879 31.7051 4.19879 31.2551 4.19879 30.6926V23.5489C4.19879 22.9864 4.64879 22.5364 5.21129 22.5364H12.355C12.9175 22.5364 13.3675 22.9864 13.3675 23.5489V30.6926Z"
                                fill="white"
                            />
                            <path
                                d="M31.0863 20.0051H23.9425C22.03 20.0051 20.455 21.5801 20.455 23.4926V30.6364C20.455 32.5489 22.03 34.1239 23.9425 34.1239H31.0863C32.9988 34.1239 34.5738 32.5489 34.5738 30.6364V23.5489C34.5738 21.5801 32.9988 20.0051 31.0863 20.0051ZM32.0988 30.6926C32.0988 31.2551 31.6488 31.7051 31.0863 31.7051H23.9425C23.38 31.7051 22.93 31.2551 22.93 30.6926V23.5489C22.93 22.9864 23.38 22.5364 23.9425 22.5364H31.0863C31.6488 22.5364 32.0988 22.9864 32.0988 23.5489V30.6926Z"
                                fill="white"
                            />
                        </svg>
                    </div>
                    <h4 class="mb-3 text-xl font-bold text-dark dark:text-white">
                        Progress Tracking
                    </h4>
                    <p class="mb-8 text-body-color dark:text-dark-6 lg:mb-9">
                        Track real results with simple charts for lifts, body measurements, and adherence streaks. Clients see "wins" like PRs and habit milestones to stay motivated.                    </p>

                </div>
            </div>
        </div>
    </div>
</section>
<!-- ====== Features Section End -->

<!-- ====== About Section Start -->
<section
    id="about"
    class="bg-gray-1 pb-8 pt-20 dark:bg-dark-2 lg:pb-[70px] lg:pt-[120px]"
>
    <div class="container px-4 mx-auto">
        <div class="wow fadeInUp" data-wow-delay=".2s">
            <div class="flex flex-wrap items-center -mx-4">
                <div class="w-full px-4 lg:w-1/2">
                    <div class="mb-12 max-w-[540px] lg:mb-0">
                        <h2
                            class="mb-5 text-3xl font-bold leading-tight text-dark dark:text-white sm:text-[40px] sm:leading-[1.2]"
                        >
                            All-in-One Platform to Coach Smarter                        </h2>
                        <p
                            class="mb-10 text-base leading-relaxed text-body-color dark:text-dark-6"
                        >
                            LiftDeck gives personal trainers everything needed to onboard clients, build programs, track nutrition, and grow their business—in one branded app.
                        </p>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<!-- ====== About Section End -->

<!-- ====== CTA Section Start -->
<section
    class="relative z-10 overflow-hidden bg-primary py-20 lg:py-[115px]"
>
    <div class="container px-4 mx-auto">
        <div class="relative overflow-hidden">
            <div class="flex flex-wrap items-stretch -mx-4">
                <div class="w-full px-4">
                    <div class="mx-auto max-w-[570px] text-center">
                        <h2
                            class="mb-2.5 text-3xl font-bold text-white md:text-[38px] md:leading-[1.44]"
                        >
                            <span>What Are You Looking For?</span>
                            <span class="text-3xl font-normal md:text-[40px]">
                    Get Started Now
                  </span>
                        </h2>
                        <p
                            class="mx-auto mb-6 max-w-[515px] text-base leading-[1.5] text-white"
                        >
                            LiftDeck is currently in closed beta. Join our select group of trainers testing the platform.
                        </p>
                        <a
                            href="mailto:beta@liftdeck.io"
                            class="inline-block rounded-md border border-transparent bg-secondary px-7 py-3 text-base font-medium text-white transition hover:bg-[#0BB489]"
                        >
                            Contact us to join closed testing
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <span class="absolute top-0 left-0">
          <svg
              width="495"
              height="470"
              viewBox="0 0 495 470"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
          >
            <circle
                cx="55"
                cy="442"
                r="138"
                stroke="white"
                stroke-opacity="0.04"
                stroke-width="50"
            />
            <circle
                cx="446"
                r="39"
                stroke="white"
                stroke-opacity="0.04"
                stroke-width="20"
            />
            <path
                d="M245.406 137.609L233.985 94.9852L276.609 106.406L245.406 137.609Z"
                stroke="white"
                stroke-opacity="0.08"
                stroke-width="12"
            />
          </svg>
        </span>
        <span class="absolute bottom-0 right-0">
          <svg
              width="493"
              height="470"
              viewBox="0 0 493 470"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
          >
            <circle
                cx="462"
                cy="5"
                r="138"
                stroke="white"
                stroke-opacity="0.04"
                stroke-width="50"
            />
            <circle
                cx="49"
                cy="470"
                r="39"
                stroke="white"
                stroke-opacity="0.04"
                stroke-width="20"
            />
            <path
                d="M222.393 226.701L272.808 213.192L259.299 263.607L222.393 226.701Z"
                stroke="white"
                stroke-opacity="0.06"
                stroke-width="13"
            />
          </svg>
        </span>
    </div>
</section>
<!-- ====== CTA Section End -->


<!-- ====== FAQ Section Start -->
<section
    class="relative z-20 overflow-hidden bg-white pb-8 pt-20 dark:bg-dark lg:pb-[50px] lg:pt-[120px]"
>
    <div class="container px-4 mx-auto">
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4">
                <div class="mx-auto mb-[60px] max-w-[520px] text-center">
              <span class="block mb-2 text-lg font-semibold text-primary">
                FAQ
              </span>
                    <h2
                        class="mb-3 text-3xl font-bold leading-[1.2] text-dark dark:text-white sm:text-4xl md:text-[40px]"
                    >
                        Any Questions? Look Here
                    </h2>
                    <p
                        class="mx-auto max-w-[485px] text-base text-body-color dark:text-dark-6"
                    >

                    </p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap -mx-4">
            <div class="w-full px-4 lg:w-1/2">
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            Is LiftDeck right for me?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            LiftDeck is perfect for personal trainers and gym coaches with 5–50 clients who want to replace spreadsheets, WhatsApp, and multiple apps with one professional platform. Ideal for hybrid or online coaching.
                        </p>
                    </div>
                </div>
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            How much does it cost?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            Currently, LiftDeck is in closed beta, but if you would like to test it, contact us at <a
                                href="mailto:beta@liftdeck.io" class="text-blue-500">beta@liftdeck.io</a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="w-full px-4 lg:w-1/2">
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            Can I customize it for my brand?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            Yes! Add your logo, colors, and gym name. Clients see your branded app, not a generic tool. Full white-label coming in V2.
                        </p>
                    </div>
                </div>
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            Do clients need to download an app?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            Clients can access their portal on mobile-friendly website portal, so they do not need to download any additional applications
                        </p>
                    </div>
                </div>
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            What about nutrition tracking?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            Full meal planning included: create templates, set macros, clients log via photos. Track adherence and get weekly nutrition reports.                        </p>
                    </div>
                </div>
                <div class="mb-12 flex lg:mb-[70px]">
                    <div
                        class="mr-4 flex h-[50px] w-full max-w-[50px] items-center justify-center rounded-xl bg-primary text-white sm:mr-6 sm:h-[60px] sm:max-w-[60px]"
                    >
                        <svg
                            width="32"
                            height="32"
                            viewBox="0 0 34 34"
                            class="fill-current"
                        >
                            <path
                                d="M17.0008 0.690674C7.96953 0.690674 0.691406 7.9688 0.691406 17C0.691406 26.0313 7.96953 33.3625 17.0008 33.3625C26.032 33.3625 33.3633 26.0313 33.3633 17C33.3633 7.9688 26.032 0.690674 17.0008 0.690674ZM17.0008 31.5032C9.03203 31.5032 2.55078 24.9688 2.55078 17C2.55078 9.0313 9.03203 2.55005 17.0008 2.55005C24.9695 2.55005 31.5039 9.0313 31.5039 17C31.5039 24.9688 24.9695 31.5032 17.0008 31.5032Z"
                            />
                            <path
                                d="M17.9039 6.32194C16.3633 6.05631 14.8227 6.48131 13.707 7.43756C12.5383 8.39381 11.8477 9.82819 11.8477 11.3688C11.8477 11.9532 11.9539 12.5376 12.1664 13.0688C12.3258 13.5469 12.857 13.8126 13.3352 13.6532C13.8133 13.4938 14.0789 12.9626 13.9195 12.4844C13.8133 12.1126 13.707 11.7938 13.707 11.3688C13.707 10.4126 14.132 9.50944 14.8758 8.87194C15.6195 8.23444 16.5758 7.96881 17.5852 8.18131C18.9133 8.39381 19.9758 9.50944 20.1883 10.7844C20.4539 12.3251 19.657 13.8126 18.2227 14.3969C16.8945 14.9282 16.0445 16.2563 16.0445 17.7969V21.1969C16.0445 21.7282 16.4695 22.1532 17.0008 22.1532C17.532 22.1532 17.957 21.7282 17.957 21.1969V17.7969C17.957 17.0532 18.382 16.3626 18.9664 16.1501C21.1977 15.2469 22.4727 12.9094 22.0477 10.4657C21.6758 8.39381 19.9758 6.69381 17.9039 6.32194Z"
                            />
                            <path
                                d="M17.0531 24.8625H16.8937C16.3625 24.8625 15.9375 25.2875 15.9375 25.8188C15.9375 26.35 16.3625 26.7751 16.8937 26.7751H17.0531C17.5844 26.7751 18.0094 26.35 18.0094 25.8188C18.0094 25.2875 17.5844 24.8625 17.0531 24.8625Z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h3
                            class="mb-6 text-xl font-semibold text-dark dark:text-white sm:text-2xl lg:text-xl xl:text-2xl"
                        >
                            How do I get started?
                        </h3>
                        <p class="text-base text-body-color dark:text-dark-6">
                            Apply for closed beta! Send us an email to <a
                                href="mailto:beta@liftdeck.io" class="text-blue-500">beta@liftdeck.io</a> and our team will contact you within 24 hours to set up your account and walk you through onboarding.                    </div>
                </div>
            </div>
        </div>
    </div>
    <div>
        <span class="absolute left-4 top-4 -z-1">
          <svg
              width="48"
              height="134"
              viewBox="0 0 48 134"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
          >
            <circle
                cx="45.6673"
                cy="132"
                r="1.66667"
                transform="rotate(180 45.6673 132)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 45.6673 117.333)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 45.6673 102.667)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 45.6673 88.0001)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="73.3335"
                r="1.66667"
                transform="rotate(180 45.6673 73.3335)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 45.6673 45.0001)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 45.6673 16.0001)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 45.6673 59.0001)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 45.6673 30.6668)"
                fill="#13C296"
            />
            <circle
                cx="45.6673"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 45.6673 1.66683)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="132"
                r="1.66667"
                transform="rotate(180 31.0013 132)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 31.0013 117.333)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 31.0013 102.667)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 31.0013 88.0001)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="73.3335"
                r="1.66667"
                transform="rotate(180 31.0013 73.3335)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 31.0013 45.0001)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 31.0013 16.0001)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 31.0013 59.0001)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 31.0013 30.6668)"
                fill="#13C296"
            />
            <circle
                cx="31.0013"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 31.0013 1.66683)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="132"
                r="1.66667"
                transform="rotate(180 16.3333 132)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 16.3333 117.333)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 16.3333 102.667)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 16.3333 88.0001)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="73.3335"
                r="1.66667"
                transform="rotate(180 16.3333 73.3335)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 16.3333 45.0001)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 16.3333 16.0001)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 16.3333 59.0001)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 16.3333 30.6668)"
                fill="#13C296"
            />
            <circle
                cx="16.3333"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 16.3333 1.66683)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="132"
                r="1.66667"
                transform="rotate(180 1.66732 132)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 1.66732 117.333)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 1.66732 102.667)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 1.66732 88.0001)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="73.3335"
                r="1.66667"
                transform="rotate(180 1.66732 73.3335)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 1.66732 45.0001)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 1.66732 16.0001)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 1.66732 59.0001)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 1.66732 30.6668)"
                fill="#13C296"
            />
            <circle
                cx="1.66732"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 1.66732 1.66683)"
                fill="#13C296"
            />
          </svg>
        </span>
        <span class="absolute bottom-4 right-4 -z-1">
          <svg
              width="48"
              height="134"
              viewBox="0 0 48 134"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
          >
            <circle
                cx="45.6673"
                cy="132"
                r="1.66667"
                transform="rotate(180 45.6673 132)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 45.6673 117.333)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 45.6673 102.667)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 45.6673 88.0001)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="73.3333"
                r="1.66667"
                transform="rotate(180 45.6673 73.3333)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 45.6673 45.0001)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 45.6673 16.0001)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 45.6673 59.0001)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 45.6673 30.6668)"
                fill="#3758F9"
            />
            <circle
                cx="45.6673"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 45.6673 1.66683)"
                fill="#3758F9"
            />
            <circle
                cx="31.0006"
                cy="132"
                r="1.66667"
                transform="rotate(180 31.0006 132)"
                fill="#3758F9"
            />
            <circle
                cx="31.0006"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 31.0006 117.333)"
                fill="#3758F9"
            />
            <circle
                cx="31.0006"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 31.0006 102.667)"
                fill="#3758F9"
            />
            <circle
                cx="31.0006"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 31.0006 88.0001)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="73.3333"
                r="1.66667"
                transform="rotate(180 31.0008 73.3333)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 31.0008 45.0001)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 31.0008 16.0001)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 31.0008 59.0001)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 31.0008 30.6668)"
                fill="#3758F9"
            />
            <circle
                cx="31.0008"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 31.0008 1.66683)"
                fill="#3758F9"
            />
            <circle
                cx="16.3341"
                cy="132"
                r="1.66667"
                transform="rotate(180 16.3341 132)"
                fill="#3758F9"
            />
            <circle
                cx="16.3341"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 16.3341 117.333)"
                fill="#3758F9"
            />
            <circle
                cx="16.3341"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 16.3341 102.667)"
                fill="#3758F9"
            />
            <circle
                cx="16.3341"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 16.3341 88.0001)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="73.3333"
                r="1.66667"
                transform="rotate(180 16.3338 73.3333)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 16.3338 45.0001)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 16.3338 16.0001)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 16.3338 59.0001)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 16.3338 30.6668)"
                fill="#3758F9"
            />
            <circle
                cx="16.3338"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 16.3338 1.66683)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="132"
                r="1.66667"
                transform="rotate(180 1.66732 132)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="117.333"
                r="1.66667"
                transform="rotate(180 1.66732 117.333)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="102.667"
                r="1.66667"
                transform="rotate(180 1.66732 102.667)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="88.0001"
                r="1.66667"
                transform="rotate(180 1.66732 88.0001)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="73.3333"
                r="1.66667"
                transform="rotate(180 1.66732 73.3333)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="45.0001"
                r="1.66667"
                transform="rotate(180 1.66732 45.0001)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="16.0001"
                r="1.66667"
                transform="rotate(180 1.66732 16.0001)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="59.0001"
                r="1.66667"
                transform="rotate(180 1.66732 59.0001)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="30.6668"
                r="1.66667"
                transform="rotate(180 1.66732 30.6668)"
                fill="#3758F9"
            />
            <circle
                cx="1.66732"
                cy="1.66683"
                r="1.66667"
                transform="rotate(180 1.66732 1.66683)"
                fill="#3758F9"
            />
          </svg>
        </span>
    </div>
</section>
<!-- ====== FAQ Section End -->



<!-- ====== Footer Section Start -->
<footer
    class="wow fadeInUp relative z-10 bg-[#090E34] pt-20 lg:pt-[100px]"
    data-wow-delay=".15s"
>


</footer>
<!-- ====== Footer Section End -->



</body>
</html>
