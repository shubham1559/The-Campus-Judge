# Sample Assignment

Here is a sample assignment for testing The Campus Judge. Add this assignment by clicking on `Add` in `Assignments` page. You can find `sample` file in `judge/samples` directory or by clicking [here](/samples/sample.zip)

## Problems

This assignment has three problems:

### Problem 1 (Divide):
  
Read two numbers a and b print a/b. errors upto 10ˆ-6 will be accepted.

| Sample Input      | Sample Output |
| ----------------- | ------------- |
| 4 2              | 2  |

### Problem 2 (Max):

Read integer n, and then read n integers. Print sum of these n numbers.

| Sample Input                       | Sample Output |
| ---------------------------------- | ------------- |
| 2<br/>4 5                          | 9             |

### Problem 3 (Upload!):

Upload a `c` or `zip` file! (This problem is "Upload Only", and will not be judged)

## Tests

Tests follow [this structure](tests_structure.md).

You can find the zip file of the tests in Assignments folder.

The tree of this file is:

```
    .
    ├── p1
    │   ├── in
    │   │   ├── input1.txt
    │   │   ├── input2.txt
    │   │   └── input3.txt
    │   ├── out
    │   │   ├── output1.txt
    │   │   └── output2.txt
    │   ├── tester.cpp
    |   ├── desc.pdf
    │   └── desc.md
    ├── p2
    │   ├── in
    │   │   ├── input1.txt
    │   │   ├── input2.txt
    │   │   └── input3.txt
    │   ├── out
    │   │   ├── output1.txt
    │   │   ├── output2.txt
    │   │   └── output3.txt
    │   ├── desc.md
    │   └── desc.pdf
    ├── p3
    |   ├── desc.pdf
    │   └── desc.md
    ├── solution
    │   └── solution.zip
    └── assignment.pdf
```



Problem 1 uses "Tester" method for checking output. So it has a file `tester.cpp` (the Tester Script)

This is the file `tester.cpp` for problem 1:

```cpp
/*
 * tester.cpp
 */
 
#include <iostream>
#include <fstream>
#include <string>
#include <cmath>
using namespace std;
int main(int argc, char const *argv[])
{

    ifstream test_in(argv[1]);    /* This stream reads from test's input file   */
    ifstream test_out(argv[2]);   /* This stream reads from test's output file  */
    ifstream user_out(argv[3]);   /* This stream reads from user's output file  */

    /* Your code here */
    /* If user's output is correct, return 0, otherwise return 1       */
    /* e.g.: Here the problem is: read a and b numbers and print a/b precision upto 10^-6:  */

    double sum, user_output;
    user_out >> user_output;

    if ( test_out.good() ) // if test's output file exists
    {
        test_out >> sum;
    }
    else
    {
        int n;
        sum=0;
        int a,b;
        test_in>>a;
        test_in>>b;
        sum=(double)a/b;
    }

    if (abs(sum-user_output)<1e-6)
        return 0;
    else
        return 1;

}
```

Problem 2 uses "Output Comparison" method for checking output. So it has two folders `in` and `out` containing test cases.

Problem 3 is an "Upload-Only" problem.

## Sample Solutions
Sample solution of all the problems is given in `solution.zip` file.