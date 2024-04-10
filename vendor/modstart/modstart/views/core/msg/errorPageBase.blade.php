<style type="text/css">
    body, html {
        font: 14px/20px normal Helvetica, Arial, "微软雅黑", sans-serif;
        color: #999;
        padding: 0;
        margin: 0;
        background: #FFF;
    }
    a {
        background: #4b4b62;
        color: #FFF;
        padding: 0 20px;
        border-radius: 1rem;
        line-height: 30px;
        text-decoration: none;
        display: inline-block;
        margin: 0 0.2rem;
    }

    .main-error-page {
        min-height: 600px;
        margin: 0px auto;
        width: auto;
        max-width: 600px;
        padding: 0 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    .error-title {
        max-width: 529px;
        font-size: 26px;
        font-weight: bold;
        font-stretch: normal;
        font-style: normal;
        line-height: normal;
        letter-spacing: normal;
        text-align: center;
        color: #4b4b62;
    }

    .error-subtitle {
        max-width: 568px;
        font-size: 16px;
        font-weight: normal;
        font-stretch: normal;
        font-style: normal;
        line-height: 1.31;
        letter-spacing: normal;
        text-align: center;
        color: #4b4b62;
        margin-top: 20px;
    }

    svg {
        margin-bottom: 16px;
    }


    .full-torradeira {

    }

    .torradeira {
    }

    .pao-atras {
        animation: leftright 1s alternate infinite;
        transform-origin: center;
    }

    .pao-frente {
        animation: leftright 1s 0.3s alternate infinite;
        transform-origin: center;
    }

    .olho-esq {

        animation: sad 2s alternate infinite;
        transform-origin: center;
    }

    .olho-dir {

        animation: sad 2s alternate infinite;
        transform-origin: center;
    }

    .boca {
        animation: sad 2s alternate infinite;
        transform-origin: center;
    }

    .raios {
        -webkit-animation: flicker-4 4s linear infinite both;
        animation: flicker-4 4s linear infinite both;
    }

    .tomada {
        -webkit-animation: vibrate-1 3s linear infinite both;
        animation: vibrate-1 3s linear infinite both;
    }

    .ani-shake {
        -webkit-animation: vibrate-1 3s linear infinite both;
        animation: vibrate-1 3s linear infinite both;
    }

    .fio {
        -webkit-animation: vibrate-1 3s linear infinite both;
        animation: vibrate-1 3s linear infinite both;
    }

    @-webkit-keyframes flicker-4 {
        0%,
        100% {
            opacity: 1;
        }
        31.98% {
            opacity: 1;
        }
        32% {
            opacity: 0;
        }
        32.8% {
            opacity: 0;
        }
        32.82% {
            opacity: 1;
        }
        34.98% {
            opacity: 1;
        }
        35% {
            opacity: 0;
        }
        35.7% {
            opacity: 0;
        }
        35.72% {
            opacity: 1;
        }
        36.98% {
            opacity: 1;
        }
        37% {
            opacity: 0;
        }
        37.6% {
            opacity: 0;
        }
        37.62% {
            opacity: 1;
        }
        67.98% {
            opacity: 1;
        }
        68% {
            opacity: 0;
        }
        68.4% {
            opacity: 0;
        }
        68.42% {
            opacity: 1;
        }
        95.98% {
            opacity: 1;
        }
        96% {
            opacity: 0;
        }
        96.7% {
            opacity: 0;
        }
        96.72% {
            opacity: 1;
        }
        98.98% {
            opacity: 1;
        }
        99% {
            opacity: 0;
        }
        99.6% {
            opacity: 0;
        }
        99.62% {
            opacity: 1;
        }
    }

    @keyframes flicker-4 {
        0%,
        100% {
            opacity: 1;
        }
        31.98% {
            opacity: 1;
        }
        32% {
            opacity: 0;
        }
        32.8% {
            opacity: 0;
        }
        32.82% {
            opacity: 1;
        }
        34.98% {
            opacity: 1;
        }
        35% {
            opacity: 0;
        }
        35.7% {
            opacity: 0;
        }
        35.72% {
            opacity: 1;
        }
        36.98% {
            opacity: 1;
        }
        37% {
            opacity: 0;
        }
        37.6% {
            opacity: 0;
        }
        37.62% {
            opacity: 1;
        }
        67.98% {
            opacity: 1;
        }
        68% {
            opacity: 0;
        }
        68.4% {
            opacity: 0;
        }
        68.42% {
            opacity: 1;
        }
        95.98% {
            opacity: 1;
        }
        96% {
            opacity: 0;
        }
        96.7% {
            opacity: 0;
        }
        96.72% {
            opacity: 1;
        }
        98.98% {
            opacity: 1;
        }
        99% {
            opacity: 0;
        }
        99.6% {
            opacity: 0;
        }
        99.62% {
            opacity: 1;
        }
    }

    @-webkit-keyframes vibrate-1 {
        0% {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
        20% {
            -webkit-transform: translate(-2px, 2px);
            transform: translate(-2px, 2px);
        }
        40% {
            -webkit-transform: translate(-2px, -2px);
            transform: translate(-2px, -2px);
        }
        60% {
            -webkit-transform: translate(2px, 2px);
            transform: translate(2px, 2px);
        }
        80% {
            -webkit-transform: translate(2px, -2px);
            transform: translate(2px, -2px);
        }
        100% {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
    }

    @keyframes vibrate-1 {
        0% {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
        20% {
            -webkit-transform: translate(-2px, 2px);
            transform: translate(-2px, 2px);
        }
        40% {
            -webkit-transform: translate(-2px, -2px);
            transform: translate(-2px, -2px);
        }
        60% {
            -webkit-transform: translate(2px, 2px);
            transform: translate(2px, 2px);
        }
        80% {
            -webkit-transform: translate(2px, -2px);
            transform: translate(2px, -2px);
        }
        100% {
            -webkit-transform: translate(0);
            transform: translate(0);
        }
    }

    @keyframes sad {
        0% {
            transform: rotateX(0deg) rotateY(0deg);
        }
        100% {
            transform: rotateX(10deg) rotateY(5deg);
        }
    }

    @keyframes leftright {

        0% {
            transform: rotateZ(0deg)
        }
        100% {
            transform: rotateZ(-15deg)
        }
    }
</style>
