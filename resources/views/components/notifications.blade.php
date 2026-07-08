@php
    $types = ['success', 'error', 'warning', 'info'];
@endphp

@foreach($types as $type)
    @if (session()->has($type))
        <div class="notification-container position-fixed">
            <div class="notification alert alert-{{ $type == 'error' ? 'danger' : $type }} alert-dismissible fade show"
                role="alert" data-duration="5000">

                <div class="notification-content">
                    <div class="notification-icon">
                        @if ($type == 'success')
                            <i class="fa-solid fa-check"></i>
                        @elseif($type == 'error')
                            <i class="fa-solid fa-xmark"></i>
                        @elseif($type == 'warning')
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        @elseif($type == 'info')
                            <i class="fa-solid fa-info"></i>
                        @endif
                    </div>

                    <div class="notification-message">
                        <span class="notification-title">
                            @if ($type == 'success')
                                Opération réussie
                            @elseif($type == 'error')
                                Une erreur est survenue
                            @elseif($type == 'warning')
                                Attention
                            @elseif($type == 'info')
                                Information
                            @endif
                        </span>

                        <p>{{ session($type) }}</p>
                    </div>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer">
                    </button>
                </div>

                <div class="notification-progress"></div>
            </div>
        </div>
    @endif
@endforeach

<style>
    .notification-container {
        top: 24px;
        right: 24px;
        z-index: 9999;
    }

    .notification {
        position: relative;
        overflow: hidden;
        width: min(460px, calc(100vw - 32px));
        min-height: 105px;
        margin: 0;
        padding: 0;
        border: none;
        border-radius: 16px;
        box-shadow: 0 14px 35px rgba(0, 0, 0, 0.22);
        animation: notificationSlideIn 0.45s ease-out;
    }

    .notification-content {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px 48px 20px 20px;
    }

    .notification-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 46px;
        height: 46px;
        border-radius: 50%;
        flex-shrink: 0;
        font-size: 1.35rem;
    }

    .notification-message {
        flex-grow: 1;
    }

    .notification-title {
        display: block;
        margin-bottom: 4px;
        font-size: 1rem;
        font-weight: 800;
    }

    .notification-message p {
        margin: 0;
        font-size: 0.93rem;
        font-weight: 500;
        line-height: 1.45;
    }

    .notification .btn-close {
        position: absolute;
        top: 18px;
        right: 18px;
        opacity: 0.55;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .notification .btn-close:hover {
        opacity: 1;
        transform: rotate(90deg);
    }

    .notification-progress {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 5px;
        transform-origin: left;
        animation: notificationProgress 5s linear forwards;
    }

    .notification:hover .notification-progress {
        animation-play-state: paused;
    }

    .notification.alert-success {
        color: #155724;
        background: linear-gradient(135deg, #effcf4 0%, #d7f5e3 100%);
        border-left: 6px solid #00A54E;
    }

    .notification.alert-success .notification-icon {
        color: #ffffff;
        background: #00A54E;
        box-shadow: 0 5px 14px rgba(0, 165, 78, 0.35);
    }

    .notification.alert-success .notification-progress {
        background: #00A54E;
    }

    .notification.alert-danger {
        color: #7a1f28;
        background: linear-gradient(135deg, #fff1f2 0%, #ffd9dd 100%);
        border-left: 6px solid #dc3545;
    }

    .notification.alert-danger .notification-icon {
        color: #ffffff;
        background: #dc3545;
        box-shadow: 0 5px 14px rgba(220, 53, 69, 0.35);
    }

    .notification.alert-danger .notification-progress {
        background: #dc3545;
    }

    .notification.alert-warning {
        color: #755500;
        background: linear-gradient(135deg, #fff9e8 0%, #ffedb1 100%);
        border-left: 6px solid #ffc107;
    }

    .notification.alert-warning .notification-icon {
        color: #4d3900;
        background: #ffc107;
        box-shadow: 0 5px 14px rgba(255, 193, 7, 0.35);
    }

    .notification.alert-warning .notification-progress {
        background: #ffc107;
    }

    .notification:hover .notification-progress {
        animation-play-state: paused;
    }
    
    .notification.alert-info {
        color: #572663;
        background: linear-gradient(135deg, #f5edff 0%, #e3d1f4 100%);
        border-left: 6px solid #91268F;
    }

    .notification.alert-info .notification-icon {
        color: #ffffff;
        background: #91268F;
        box-shadow: 0 5px 14px rgba(145, 38, 143, 0.35);
    }

    .notification.alert-info .notification-progress {
        background: #91268F;
    }

    @keyframes notificationSlideIn {
        from {
            opacity: 0;
            transform: translateX(120%);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes notificationProgress {
        from {
            transform: scaleX(1);
        }

        to {
            transform: scaleX(0);
        }
    }

    @media (max-width: 576px) {
        .notification-container {
            top: 15px;
            right: 15px;
            left: 15px;
        }

        .notification {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const notifications = document.querySelectorAll('.notification-container .notification');

        notifications.forEach(function(notification) {
            const duration = Number(notification.dataset.duration || 5000);
            let remainingTime = duration;
            let isHovered = false;
            let lastTime = Date.now();
            let timerId = null;

            function updateTimer() {
                if (isHovered) {
                    // Si survol, ne pas diminuer le temps
                    timerId = setTimeout(updateTimer, 100);
                    return;
                }

                const now = Date.now();
                const elapsed = now - lastTime;
                lastTime = now;
                remainingTime -= elapsed;

                if (remainingTime <= 0) {
                    const bootstrapAlert = bootstrap.Alert.getOrCreateInstance(notification);
                    bootstrapAlert.close();
                } else {
                    timerId = setTimeout(updateTimer, 100);
                }
            }

            timerId = setTimeout(updateTimer, 100);

            notification.addEventListener('mouseenter', function() {
                isHovered = true;
            });

            notification.addEventListener('mouseleave', function() {
                isHovered = false;
                lastTime = Date.now();
            });

            notification.addEventListener('closed.bs.alert', function() {
                if (timerId) {
                    clearTimeout(timerId);
                }
            });
        });
    });
</script>
