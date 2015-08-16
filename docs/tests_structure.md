Tests Structure
===============

When adding assignments, you must provide a zip file containing test cases,description and solution. Everything is optional. You can edit this file later and upload again if you want any change. For a sample you may view [Sample assignment](sample_assignment.md).

There are two methods of checking output for each problem: “Input/Output Comparison” method and “Tester” method:

Input/Output Comparison method
------------------------------

In this method, you must put some input and output files in the problem's folder. The Campus Judge gives each test's input file to user's code and compares user's output with the test's output. Input files must be in folder `in` with names input1.txt, input2.txt, … and output files must be in folder `out` with names output1.txt, output2.txt, …

Tester method
-------------

In this method, you must provide some input test files and a C++ file (tester.cpp) and (optionally) some output test files. The Campus Judge gives the input test file to user's code and gets the user's output. Then tester.cpp gets test's input, test's output and user's output. If the user's output is correct, tester.cpp should return 0, otherwise return 1.

You can use this code template for writing tester.cpp:

```cpp
/*
 * tester.cpp
 */
 
#include <iostream>
#include <fstream>
#include <string>
using namespace std;
int main(int argc, char const *argv[])
{
 
	ifstream test_in(argv[1]);    /* This stream reads from test's input file   */
	ifstream test_out(argv[2]);   /* This stream reads from test's output file  */
	ifstream user_out(argv[3]);   /* This stream reads from user's output file  */
 
	/* Your code here */
	/* If user's output is correct, return 0, otherwise return 1       */
 
	...
 
}
```

Structure
-------------

In the zip file you should have a folder for each problem named p1,p2,... .
Each of these folders should have a markdown file like desc.md. This file will be converted to html internallly. You can also have a pdf file which students can download from the problem page. Along with these files you must have input/output files and (optionally) tester file according to your judging method described above. You can edit description file in your browser too. But you must upload pdf file again if you want any changes in pdf too. 

You can have pdf for the whole assignment which can be downloaded from `Assignments` page. this must should be in root of the zip file. 

You can have a solution ZIP file which can be downloaded by students after the assignment is made public. from `Assignments` page. this file should be kept in a folder named `solution`. 

You can find a sample tests file in `judge/samples` folder of The Campus Judge.
You can find the details of sample tests file [here](sample_assignment.md)

This is the tree for the sample file
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


Convert markdown to pdf
--------------------
You can easily generate pdf files for assignment and problems if you have made the markdown file. If you have pandoc and latex installed then you can use scripts in `judge/samples/sample_with_resources.zip` file. For details see [this](assignment_helper.md)


