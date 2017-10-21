# do-not-follow
트위터 팔로워의 프로필 자기소개에 특정 단어가 있을 때 자동 블락 처리하는 서비스의 웹 사이트, Java 프로그램 코드입니다.

- 웹 사이트를 통해 설정(차단 단어, 블언블 여부 등), 차단된 계정 목록을 확인할 수 있습니다.
- java 폴더: DB에 등록된 계정 정보로 팔로워를 확인해 블락 처리하는 프로그램입니다.
  - 문제점: 트위터의 GET followers/list API 제한이 15분 당 15번인데, 2분 마다 작동시켰음에도 불구하고 트위터에서 비정상 활동으로 체크하는 경우가 있는 듯합니다.

### 사용된 라이브러리
https://github.com/abraham/twitteroauth  
https://github.com/yusuke/twitter4j
