<!DOCTYPE html>
<html lang="en-US">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<h2>패스워드 재설정</h2>
		<p>당신의 패스워드를 리셋합니다, <a href="{{ URL::to('user') }}/password/{{ $userId }}/{{ urlencode($resetCode) }}">여기를 클릭하기.</a>  당신이 패스워드 재설정을 요청하지 않는 경우, 안전하게 이메일을 무시할 수 있습니다. - 아무것도 변하지 않습니다.</p>
		<p>아니면 이 주소로 당신의 브라우져에서 요청하세요: <br /> {{ URL::to('user') }}/password/{{ $userId }}/{{ urlencode($resetCode) }}</p>
		<p>감사합니다, <br />
			~파이페이먼트 관리팀</p>
	</body>
</html>