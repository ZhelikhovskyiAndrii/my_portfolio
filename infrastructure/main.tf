provider "aws" {
  region     = "eu-central-1"
  
}

resource "aws_vpc" "actpro-vpc" {
     cidr_block = "10.0.0.0/16"
      tags = {
        Name = "actpro-net"
  }
}

resource "aws_subnet" "front-end-net" {
  vpc_id     = aws_vpc.actpro-vpc.id
  cidr_block = "10.0.1.0/24"

  tags = {
    Name = "public-net"
  }
}

resource "aws_subnet" "back-end-net" {
  vpc_id     = aws_vpc.actpro-vpc.id
  cidr_block = "10.0.2.0/24"

  tags = {
    Name = "private-net"
  }
}

resource "aws_internet_gateway" "actpro-gw" {
  vpc_id = aws_vpc.actpro-vpc.id

  tags = {
    Name = "actpro-gw"
  }
}

resource "aws_route_table" "actpro-rt" {
  vpc_id = aws_vpc.actpro-vpc.id

  route {
    cidr_block = "0.0.0.0/0"
    gateway_id = aws_internet_gateway.actpro-gw.id
  } 

  tags = {
    Name = "actpro-rt-front"
  }
}

resource "aws_route_table_association" "a-front-net" {
  subnet_id      = aws_subnet.front-end-net.id
  route_table_id = aws_route_table.actpro-rt.id
}

resource "aws_security_group" "actpro-sg" {
  name        = "ssh-web"
  description = "Allow 22 and 80 ports traffic"
  vpc_id      = aws_vpc.actpro-vpc.id

  ingress {
    description      = "SSH from VPC"
    from_port        = 22
    to_port          = 22
    protocol         = "tcp"
    cidr_blocks      = ["0.0.0.0/0"]
  }

  ingress {
    description      = "app from web"
    from_port        = 3000
    to_port          = 3000
    protocol         = "tcp"
    cidr_blocks      = ["0.0.0.0/0"]
  }
ingress {
    description      = "Postgres"
    from_port        = 5432
    to_port          = 5432
    protocol         = "tcp"
    cidr_blocks      = ["10.0.0.0/16"]
  }
  ingress {
    description      = "WEB from VPC"
    from_port        = 80
    to_port          = 80
    protocol         = "tcp"
    cidr_blocks      = ["0.0.0.0/0"]
  }
  ingress {
    description      = "HTTPS"
    from_port        = 443
    to_port          = 443
    protocol         = "tcp"
    cidr_blocks      = ["0.0.0.0/0"]
  }
  ingress {
    description      = "WEB app ver.2"
    from_port        = 8080
    to_port          = 8080
    protocol         = "tcp"
    cidr_blocks      = ["0.0.0.0/0"]
  }
  ingress {
    description      = "icmp"
    from_port        = -1
    to_port          = -1
    protocol         = "icmp"
    cidr_blocks      = ["10.0.0.0/16"]
  }
  
  egress {
    from_port        = 0
    to_port          = 0
    protocol         = "-1"
    cidr_blocks      = ["0.0.0.0/0"]
  }

  tags = {
    Name = "ssh-web-sg"
  }
}

resource "aws_security_group" "actpro-back-end-sg" {
  name        = "back-end-sg"
  description = "for back-end"
  vpc_id      = aws_vpc.actpro-vpc.id

  ingress {
    description      = "SSH from VPC"
    from_port        = 22
    to_port          = 22
    protocol         = "tcp"
    cidr_blocks      = ["10.0.1.0/24"]
  }

  ingress {
    description      = "Postgres"
    from_port        = 5432
    to_port          = 5432
    protocol         = "tcp"
    cidr_blocks      = ["10.0.0.0/16"]
  }
  
  ingress {
    description      = "icmp"
    from_port        = -1
    to_port          = -1
    protocol         = "icmp"
    cidr_blocks      = ["10.0.0.0/16"]
  }

  egress {
    from_port        = 0
    to_port          = 0
    protocol         = "-1"
    cidr_blocks      = ["0.0.0.0/0"]
  }

  tags = {
    Name = "back-end-sg"
  }
}

resource "aws_instance" "k8-worker1" {
  ami           = data.aws_ami.ubuntu-latest.id
  instance_type = "t2.micro"
  subnet_id = aws_subnet.front-end-net.id
  vpc_security_group_ids = [aws_security_group.actpro-sg.id]
  associate_public_ip_address = true

  key_name = "terraform-key-pem"
  
  tags = {
    Name = "k8-worker1"
  }
}
resource "aws_instance" "k8-worker2" {
  ami           = data.aws_ami.ubuntu-latest.id
  instance_type = "t2.micro"
  subnet_id = aws_subnet.front-end-net.id
  vpc_security_group_ids = [aws_security_group.actpro-sg.id]
  associate_public_ip_address = true

  key_name = "terraform-key-pem"
  
  tags = {
    Name = "k8-worker2"
  }
}
resource "aws_instance" "k8-worker3" {
  ami           = data.aws_ami.ubuntu-latest.id
  instance_type = "t2.micro"
  subnet_id = aws_subnet.front-end-net.id
  vpc_security_group_ids = [aws_security_group.actpro-sg.id]
  associate_public_ip_address = true
  key_name = "terraform-key-pem"
  tags = {
    Name = "k8-worker3"
  }
}

resource "aws_instance" "k8s-master1" {
  ami           = data.aws_ami.ubuntu-latest.id
  instance_type = "t2.medium"
  subnet_id = aws_subnet.front-end-net.id
  vpc_security_group_ids = [aws_security_group.actpro-sg.id]
  associate_public_ip_address = true

  key_name = "terraform-key-pem"
  
  tags = {
    Name = "k8s-master1"
  }
}
