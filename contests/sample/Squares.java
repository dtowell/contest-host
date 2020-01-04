import java.util.Scanner;
public class Squares {
    public static void main(String[] args) {
        Scanner in = new Scanner(System.in);
        int n = in.nextInt();
        for (int i=0; i<n; i++) {
            int x = in.nextInt();
            System.out.println(x*x);
        }
    }
}
