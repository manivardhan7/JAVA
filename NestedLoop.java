//Nested loop : it is used to place a loop inside another loop. This is called Nested loop
//The inner loop will be executed one time for each iteration of the "OUTERLOOP"
//lets take a basic example
public class NestedLoop {

	public static void main(String[] args) {
		for(int i=1;i<2;i++) {//Here i have taken only one outer loop 
			System.out.println("Outer : "  + i);{
				for (int j = 1; j <= 3; j++) {// here i have assigned it to loop 3 times inside outer loop
					System.out.println("Inner : " + j);
				}
				
			}
			
		}
		
	}

}
