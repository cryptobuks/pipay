<div id="pi_refund_widget" class="architekt-widget-custom architekt-widget-background">
    <form action="" method="post" id="pi_refund_widget_container" class="architekt-widget-container">
        <div id="pi_refund_top">
            <h1>사용가능한 잔고</h1>
            <p data-architekt-key="balance" data-architekt-format="currency" data-architekt-format-args="symbol:pi"></p>
        </div>

        <div id="pi_refund_info">
            파이페이 계정에서 사용할 수 있는 잔액입니다. 환불할 금액이 부족한 경우 pi-pay.net에서 충전하실 수 있습니다.
        </div>

        <div id="pi_refund_fields">
            <!-- address for refunding -->
            <div class="pi-form-control">
                <label for="address">환불 받을 주소</label>
                <input type="text" id="" class="pi-input" name="address" value="" >
            </div>
            <!-- refunding amount -->
            <div class="pi-form-control">
                <label for="amount">환불 금액</label>
                <input data-architekt-key="amount" type="text" id="" class="pi-input" name="amount" value="" >

                <div class="pi-checkbox">
                    <input type="checkbox" id="partial" name="" />
                    <label for="partial">분할환불</label>
                </div>
            </div>
        </div>

        <div class="pi-button-container pi-button-centralize">
            <input class="architekt-widget-button architekt-widget-close" type="button" value="취소" />
            <input class="architekt-widget-button architekt-theme-confirm" type="submit" value="확인" />
        </div>
    </div>
</div>