/* Array: it is a collection of elements of the same dattype stored in contigenous memory locations'
 * Declaring and initalising array : Single directional array.
 * data type[] array name = newdatatype[size]
 
 
 single directional
 syntax:
public class Arrays {

	public static void main(String[] args) {
		int[] numbers = new int[5];
		numbers[0] =10;
		numbers[1] =20;
	}

}

multi directional array:
syntax: datatype[][] arrayname = new datatype[rows][coloums]
ex:int[][] matrix = new int[3][3]


Transversaing Array:
1.Using a for loop
for(int i = 0;) i<number.length;i++{
system.out.println(number[i]);
}

2. using an enclosed forloop:
for(int num:numbers{
system,out.println(num);
}
}*/

public class Arrays {

	public static void main(String[] args) { // main method
   int[] numbers = {10,20,30,40,50};
   int sum = 0;// printing array elements
		for(int num : numbers) {
			sum+= num;
			System.out.println("sum " + sum);
		}
		//finding the largest number 
		int max = numbers[0];
		for(int num : numbers) {
			if(num > max) {
				max = num;
			}
		}
		System.out.println("largest number " + max);
	}
	

	}
