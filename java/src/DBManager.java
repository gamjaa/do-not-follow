import java.sql.*;
import java.util.ArrayList;
import java.util.List;

/**
 * Created by jeong on 2017-06-03.
 */
public class DBManager {
    String driver        = "org.mariadb.jdbc.Driver";
    String url           = "jdbc:mariadb://localhost:3306/do-not-follow";
    String uId           = Config.DB_ID;
    String uPwd          = Config.DB_PW;

    Connection con;
    PreparedStatement pstmt;
    ResultSet rs;
    List<Users> users = new ArrayList<Users>();

    public DBManager() {
        try {
            Class.forName(driver);
            con = DriverManager.getConnection(url, uId, uPwd);

            if( con != null ){ System.out.println("Database Connect Success"); }

        } catch (ClassNotFoundException e) { System.out.println("Driver Load Fail ***");    }
        catch (SQLException e) { System.out.println("Database Connect Fail ***"); }
    }

    public void select(){
        String sql    = "SELECT * FROM users WHERE switch=1 and words is not null and words != ''";
        try {
            pstmt                = con.prepareStatement(sql);
            rs                   = pstmt.executeQuery();
            while(rs.next()){
                users.add(new Users(rs.getLong("user_id"), rs.getString("oauth_token"), rs.getString("oauth_token_secret"), rs.getInt("isBlock"), rs.getString("words")));
            }
        } catch (SQLException e) { System.out.println("Select Query Execute Fail ***"); }
    }

    public void insert(long user, long follower, String word, String description) {
        String sql    = "INSERT INTO block (user, follower, word, description) VALUES ("+user+", "+follower+", '"+word+"', '"+description+"'"+")";
        try {
            pstmt                = con.prepareStatement(sql);
            rs                   = pstmt.executeQuery();
        } catch (SQLException e) { System.out.println("Insert Query Execute Fail ***"); }
    }

    public void update(long user, long follower) {
        String sql    = "UPDATE block SET isUnblockAuto = 1 WHERE user = "+user+" and follower = "+follower;
        try {
            pstmt                = con.prepareStatement(sql);
            rs                   = pstmt.executeQuery();
        } catch (SQLException e) { System.out.println("Update Query Execute Fail ***"); }
    }
}