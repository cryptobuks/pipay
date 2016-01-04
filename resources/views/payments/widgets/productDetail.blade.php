    <div id="pi_product_widget" class="architekt-widget-custom architekt-widget-background">
        <div id="pi_product_widget_container" class="architekt-widget-container">
            <div id="pi_product_widget_close" class="architekt-widget-close">×</div>

            <table>
                <tbody>
                    <tr>
                        <td data-architekt-key="item_desc" class="pi-text-center" colspan="3"></td>
                    </tr>
                    <tr>
                        <td class="pi-text-center">
                            <h1 data-architekt-key="amount" data-architekt-format="currency" data-architekt-format-args="symbol:krw"></h1>
                            <p>전체(KRW)</p>
                        </td>
                        <td class="pi-text-center">
                            <h1 data-architekt-key="pi_amount" data-architekt-format="currency" data-architekt-format-args="symbol:pi"></h1>
                            <p>전체(PI)</p>
                        </td>
                        <td class="pi-text-center">
                            <h1 data-architekt-key="pi_amount_received" data-architekt-format="currency" data-architekt-format-args="symbol:pi"></h1>
                            <p>결제완료(PI)</p>
                        </td>
                    </tr>
                    <tr>
                        <td id="pi_product_info" colspan="3">
                            <div>
                                <h1>고객 정보</h1>
                                <p>이메일 주소: <span data-architekt-key="customer_email" data-architekt-format="printIfHasValue"></span></p>
                                <p>이름: <span data-architekt-key="customer_name" data-architekt-format="printIfHasValue"></span></p>
                                <p>상품번호: <span data-architekt-key="customer_custom" data-architekt-format="printIfHasValue"></span></p>
                            </div>
                            <div>
                                <h1>결제 정보</h1>
                                <p>상태: <span data-architekt-key="status" data-architekt-format="statusText"></span></p>
                                <p>결제통화: <span data-architekt-key="currency" data-architekt-format="printIfHasValue" data-architekt-format-args="pi"></span></p>
                                <p>결제시각: <span data-architekt-key="created_at" data-architekt-format="printIfHasValue"></span></p>
                                <p>결제완료: <span data-architekt-key="completed_at" data-architekt-format="printIfHasValue"></span></p>
                            </div>

                            <a id="refund" href="#">환불하기 &gt;</a>
                            <a id="receipt" href="#">영수증 보기 &gt;&gt;</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>