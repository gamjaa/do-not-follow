import twitter4j.*;
import twitter4j.auth.AccessToken;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Calendar;

/**
 * Created by jeong on 2017-06-03.
 */
public class DoNotFollow extends Thread {
    static final String CONSUMER_KEY = Config.CONSUMER_KEY;
    static final String CONSUMER_SECRET = Config.CONSUMER_SECRET;

    static DBManager db = new DBManager();

    Users user;
    String OAUTH_TOKEN, OAUTH_TOKEN_SECRET;

    public DoNotFollow(Users user, String OAUTH_TOKEN, String OAUTH_TOKEN_SECRET) {
        this.user = user;
        this.OAUTH_TOKEN = OAUTH_TOKEN;
        this.OAUTH_TOKEN_SECRET = OAUTH_TOKEN_SECRET;
    }

    public void run() {
        String time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
        System.out.println(time + this.user.user_id + " START");

        Twitter twitter = new TwitterFactory().getInstance();
        twitter.setOAuthConsumer(CONSUMER_KEY, CONSUMER_SECRET);
        twitter.setOAuthAccessToken(new AccessToken(this.OAUTH_TOKEN, this.OAUTH_TOKEN_SECRET));

        long cursor;

        String[] words = this.user.words.split(",");    // 단어 목록 파싱
        time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getInstance().getTime()));
        System.out.println(time + this.user.user_id + "의 단어 목록: " + Arrays.toString(words));

        boolean isProtected = false;
        try {
            isProtected = twitter.showUser(this.user.user_id).isProtected();  // 나의 플텍 여부 확인
        } catch (TwitterException e) {
            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
            System.out.println(time + this.user.user_id+"의 플텍 여부 확인 실패 ***");
        }
        if(isProtected) {
            IDs incomingFollowers = null;
            try {
                incomingFollowers = twitter.getIncomingFriendships(cursor = -1);    // 팔로잉 대기자 확인
            } catch (TwitterException e) {
                time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                System.out.println(time + this.user.user_id+"의 팔로잉 대기자 확인 실패 ***");
            }

            for (long incomingFollower : incomingFollowers.getIDs()) {
                String description = null;
                try {
                    description = twitter.showUser(incomingFollower).getDescription().replaceAll("[^A-Za-z0-9ㄱ-ㅎㅏ-ㅣ가-힣]", "");
                } catch (TwitterException e) {
                    time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                    System.out.println(time + this.user.user_id+"의 "+incomingFollower+" 자기소개문 확인 실패 ***");
                    continue;
                }
                for(String word : words) { // 단어 목록
                    if(description.contains(word)) { // 설명에 단어가 있을 때
                        Relationship relationship = null;
                        try {
                            relationship = twitter.showFriendship(this.user.user_id, incomingFollower);
                        } catch (TwitterException e) {
                            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                            System.out.println(time + this.user.user_id+"와 "+incomingFollower+"의 관계 확인 실패 ***");
                            break;
                        }
                        if(!relationship.isSourceFollowingTarget()) {   // 내가 팔로우 중이지 않으면
                            try {
                                twitter.createBlock(incomingFollower);  // 블록 처리
                            } catch (TwitterException e) {
                                time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                                System.out.println(time + this.user.user_id+"의 "+incomingFollower+" 블락 실패 ***");
                                break;
                            }

                            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                            System.out.println(time + this.user.user_id+"가 "+incomingFollower+" 블락");    // 블록 로그
                            db.insert(this.user.user_id, incomingFollower, word, description);   // DB 저장

                            if(this.user.isBlock == 0) {    // 블언블 기능 사용자
                                try {
                                    twitter.destroyBlock(incomingFollower);
                                } catch (TwitterException e) {
                                    time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                                    System.out.println(time + this.user.user_id+"의 "+incomingFollower+" 언블락 실패 ***");
                                    break;
                                }

                                time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                                System.out.println(time + this.user.user_id+"가 "+incomingFollower+" 언블락");    // 언블록 로그
                                db.update(this.user.user_id, incomingFollower);
                            }
                        }
                        break;
                    }
                }
            }
        }

        //do {
        PagableResponseList<User> followers = null;
        try {
            followers = twitter.getFollowersList(this.user.user_id, cursor = -1);
        } catch (TwitterException e) {
            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
            System.out.println(time + this.user.user_id+"의 팔로워 로드 실패 ***");
            return;
        }
        for(User follower : followers) {
            String description = follower.getDescription().replaceAll("[^A-Za-z0-9ㄱ-ㅎㅏ-ㅣ가-힣]", "");
            for(String word : words) { // 단어 목록
                if(description.contains(word)) { // 설명에 단어가 있을 때
                    Relationship relationship = null;
                    try {
                        relationship = twitter.showFriendship(this.user.user_id, follower.getId());
                    } catch (TwitterException e) {
                        time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                        System.out.println(time + this.user.user_id+"와 "+follower.getId()+"의 관계 확인 실패 ***");
                        break;
                    }
                    if(!relationship.isSourceFollowingTarget()) {   // 내가 팔로우 중이지 않으면
                        try {
                            twitter.createBlock(follower.getId());  // 블록 처리
                        } catch (TwitterException e) {
                            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                            System.out.println(time + this.user.user_id+"의 "+follower.getId()+" 블락 실패 ***");
                            break;
                        }

                        time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                        System.out.println(time + this.user.user_id+"가 "+follower.getId()+" 블락");    // 블록 로그
                        db.insert(this.user.user_id, follower.getId(), word, description);   // DB 저장

                        if(this.user.isBlock == 0) {    // 블언블 기능 사용자
                            try {
                                twitter.destroyBlock(follower.getId());
                            } catch (TwitterException e) {
                                time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                                System.out.println(time + this.user.user_id+"의 "+follower.getId()+" 언블락 실패 ***");
                                break;
                            }

                            time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
                            System.out.println(time + this.user.user_id+"가 "+follower.getId()+" 언블락");    // 언블록 로그
                            db.update(this.user.user_id, follower.getId());
                        }
                    }
                    break;
                }
            }
        }
        //} while((cursor = followers.getNextCursor()) != 0);

        time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
        System.out.println(time + this.user.user_id + " END");
    }
    public static void main(String[] args) {
        String time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
        System.out.println(time + "동작 시작");

        db.select();

        ArrayList<Thread> threads = new ArrayList<Thread>();

        for(Users user : db.users){
            Thread t = new DoNotFollow(user, user.oauth_token, user.oauth_token_secret);
            t.start();
            threads.add(t);
            try {
                Thread.sleep(100);
            } catch (InterruptedException e) {}
        }

        for(int i=0; i<threads.size(); i++) {
            Thread t = threads.get(i);
            try {
                t.join();
            }catch(Exception e) {
            }
            try {
                Thread.sleep(100);
            } catch (InterruptedException e) {}
        }

        time = (new SimpleDateFormat("[yyyy-MM-dd HH:mm:ss] ").format(Calendar.getInstance().getTime()));
        System.out.println(time + "동작 종료");
    }
}
