#How to generate pdfs
A sample assignment file with resources is given in `judge/samples` folder.
Extract this file. the tree of this file is 
```
.
├── assignment.pdf
├── p1
│   ├── desc.md
│   ├── desc.pdf
│   ├── in
│   │   ├── input1.txt
│   │   ├── input2.txt
│   │   └── input3.txt
│   ├── out
│   │   ├── output1.txt
│   │   └── output2.txt
│   └── tester.cpp
├── p2
│   ├── desc.md
│   ├── desc.pdf
│   ├── in
│   │   ├── input1.txt
│   │   ├── input2.txt
│   │   └── input3.txt
│   └── out
│       ├── output1.txt
│       ├── output2.txt
│       └── output3.txt
├── p3
│   ├── desc.md
│   └── desc.pdf
├── scripts
│   ├── banner.png
│   ├── bash
│   ├── frontpage.md
│   ├── README.md
│   └── template.latex
└── solution
    └── solution.zip

```
scripts folder cantains some useful code snnippets to convert description files to pdf documents. A sample desc.md file is given for all problems in their respective folders.

 `bash` is a bash script file; running this will generate pdf files for all the problems and for the assignment from desc.md files. To use it `cd path/to/sources/directory` and  `sh bash`. bash file uses pandoc and xelatex to generate pdf files. these two must be installed. On windows you can use
 You may want to change your assignment name and authors name; to do that
 change this in template.latex file.
```
 \fancyhead[CO,CE]{The assignment name}
```
and change this in frontpage.md file 
```
% ![The Campus Judge](sources/banner.png)    
   The assignment name
% Author One
  Author Two
```

```
 pandoc "p1/desc.md" --latex-engine=xelatex --template sources/template.latex -o "p1/desc.pdf"
```

this will generate pdf for that particular problem
use this to generate pdf for the whole assignment

```
pandoc sources/frontpage.md p1/desc.md p2/desc.md p3/desc.md --latex-engine=xelatex --template sources/template.latex -o assignment.pdf
```
Delete the scripts folder after you are done. The data is same as the [Sample assignment](sample_assignment.md)
