/**
 * Created by jeong on 2017-06-03.
 */
public class Users {
    long user_id;
    String oauth_token;
    String oauth_token_secret;
    int isBlock;
    String words;

    public Users(long USER_ID, String OAUTH_TOKEN, String OAUTH_TOKEN_SECRET, int ISBLOCK, String WORDS) {
        user_id = USER_ID;
        oauth_token = OAUTH_TOKEN;
        oauth_token_secret = OAUTH_TOKEN_SECRET;
        isBlock = ISBLOCK;
        words = WORDS;
    }
}
