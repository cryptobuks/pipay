window.Locale = new function () {
    var currentLocale = 'ko';
    var localeString = {
        'en': {
            'requiredEmail': 'Please type the email.',
            'requiredPw': 'Please type the password.',
            'requiredName': 'Please type your name.',
            'requiredPwc': 'Please retype the password.',
            'requiredCP': 'Please type your mobile number.',
            'requiredCurrentPw': 'Please enter your current password.',
            'requiredNewPw': 'Please enter the new password.',
            'requiredNewPwc': 'Please retype the new password.',
            'passwordNotMatch': 'Passwords are not matched.',
            'newPasswordNotMatch': 'New passwords are not matched',
            'typeOrder': 'Please type the amount.',
            'typeWithdrawAmount': 'Please type the amount.',
            'typeDestAddress': 'Please type the address.',
            'typeChargeAmount': 'Please type the amount.',
            'typeRefundAmount': 'Please type the amount.',
            'typeAddress': 'Please type the address.',
            'buttonOk': 'Ok',
            'buttonCancel': 'Cancel',
            'orderCancel': 'Do you want to cancel?',
            'withdrawCancel': 'Do you want to cancel?',            
            'refundCancel': 'Do you want to cancel?',                        
            'chargeCancel': 'Do you want to cancel?',      
            'namecheckPopup':'Are you sure to authenticate yourself with a cell phone?' ,                                         
            'namecheckChange':"You can change the cell phone number only your own. Are you sure to change your phone number?" ,                                                     
        },
        'ko': {
            'requiredEmail': '이메일 주소를 입력해주세요.',
            'requiredPw': '비밀번호를 입력해주세요.',
            'requiredName': '이름을 입력해주세요.',
            'requiredPwc': '비밀번호 확인을 입력해주세요.',
            'requiredCP': '휴대폰 번호를 입력해주세요.',
            'requiredCurrentPw': '현재 비밀번호를 입력해주세요.',
            'requiredNewPw': '새 비밀번호를 입력해주세요.',
            'requiredNewPwc': '새 비밀번호 확인을 입력해주세요.',
            'passwordNotMatch': '비밀번호가 일치하지 않습니다.',
            'newPasswordNotMatch': '새 비밀번호가 일치하지 않습니다.',
            'typeOrder': '구매량을 입력하세요.',
            'typeWithdrawAmount': '출금액을 입력해주세요.',
            'typeDestAddress': '받는 사용자의 주소를 입력해주세요.',
            'typeChargeAmount': '충전액을 입력해주세요.',
            'typeRefundAmount': '출금액을 입력해주세요.',
            'typeAddress': '계좌번호를 입력해주세요.',
            'buttonOk': '확인',
            'buttonCancel': '취소',
            'orderCancel' : '주문을 취소하시겠습니까?',
            'withdrawCancel': '출금 요청을 취소하시겠습니까?', 
            'refundCancel': '환급 요청을 취소하시겠습니까?',                                         
            'chargeCancel': '충전 요청을 취소하시겠습니까?',     
            'namecheckPopup':'휴대폰을 사용한 본인 인증을 하시겠습니까?' ,
            'namecheckChange':"본인 명의의 휴대폰인 경우에만 변호 변경이 가능합니다. 휴대폰 번호를 변경하시겠습니까?" ,            
        }
    };

    this.setLocale = function(locale) {
        currentLocale = locale;
        return this;
    };
    this.getLocale = function() {
        return locale;
    };
    this.getString = function (key) {
        if (localeString[currentLocale][key]) return localeString[currentLocale][key];
        return key;
    };
};