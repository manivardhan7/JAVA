

public class SpecialChar {
	public static void main(String[] args) {
		String text = "Hello\rWorld";//carriage returns means  it moves the cursor to the beginning of the line.
		String M = "Hello\tWorld";//gives tab space
		String A = "Hello\nWorld";//it breaks the line and starts a new line
		System.out.println(text);
		System.out.println(M);
		System.out.println(A);
		System.out.println("Hello\b World!");// remove one step back in hello it removes 'o'.
		 System.out.println("First Line\fSecond Line");//to break the line
		
	}

}
